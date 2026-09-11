<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EvaluationStatus;
use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationFinding;
use App\Models\EvaluationVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class EvaluationService
{
    public function startOrResume(Business $business): Evaluation
    {
        $existing = $business->evaluations()->whereIn('status', [EvaluationStatus::Draft, EvaluationStatus::InProgress])->latest('id')->first();
        if ($existing !== null) {
            return $existing->load('version.sections.questions', 'answers');
        }

        $version = EvaluationVersion::query()->where('is_active', true)->latest('id')->first();
        if ($version === null) {
            throw ValidationException::withMessages(['evaluation' => 'No active evaluation is available yet.']);
        }

        $evaluation = $business->evaluations()->create([
            'evaluation_version_id' => $version->id,
            'status' => EvaluationStatus::InProgress,
            'started_at' => now(),
        ]);

        return $evaluation->load('version.sections.questions', 'answers');
    }

    public function saveAnswer(Evaluation $evaluation, string $questionKey, mixed $value): EvaluationAnswer
    {
        $this->ensureEditable($evaluation);
        $question = $evaluation->version->sections->flatMap->questions->firstWhere('key', $questionKey);
        if ($question === null) {
            throw ValidationException::withMessages(['answer' => 'The selected question does not belong to this evaluation version.']);
        }

        $rules = $question->validation_rules ?? [];
        if ($question->required) {
            $rules = array_merge(['required'], $rules);
        }
        validator(['value' => $value], ['value' => $rules])->validate();

        $evaluation->update(['status' => EvaluationStatus::InProgress]);

        return $evaluation->answers()->updateOrCreate(
            ['question_key' => $questionKey],
            ['value' => ['answer' => $value]],
        );
    }

    public function complete(Evaluation $evaluation): Evaluation
    {
        $this->ensureEditable($evaluation);
        $questions = $evaluation->version->sections->flatMap->questions;
        $answers = $evaluation->answers->keyBy('question_key');
        foreach ($questions as $question) {
            if ($question->required && ! $answers->has($question->key)) {
                throw ValidationException::withMessages(['evaluation' => 'Please answer all required questions before completing the evaluation.']);
            }
        }

        return DB::transaction(function () use ($evaluation): Evaluation {
            $evaluation->update(['status' => EvaluationStatus::Completed, 'completed_at' => now()]);
            $this->diagnose($evaluation->fresh(['version.sections.questions', 'answers']));

            return $evaluation->fresh(['version.sections.questions', 'answers', 'findings']);
        });
    }

    /** @return array<int, EvaluationFinding> */
    public function diagnose(Evaluation $evaluation): array
    {
        $evaluation->findings()->delete();
        $answers = $evaluation->answers->keyBy('question_key');
        $findings = [];
        foreach ($evaluation->version->sections as $section) {
            $answered = $section->questions->filter(fn ($question): bool => $answers->has($question->key))->count();
            $total = $section->questions->count();
            $ratio = $total > 0 ? $answered / $total : 0;
            $severity = $ratio < 0.5 ? 'high' : ($ratio < 1 ? 'medium' : 'low');
            $findings[] = $evaluation->findings()->create([
                'dimension' => $section->key,
                'severity' => $severity,
                'title' => $section->title,
                'description' => $ratio === 1.0
                    ? 'This area was fully assessed. Review the answers and use the next steps to decide what matters most.'
                    : sprintf('This area is only %d%% assessed. Complete the missing answers before relying on this diagnosis.', (int) round($ratio * 100)),
                'context' => ['answered' => $answered, 'total' => $total, 'version' => $evaluation->version->version],
            ]);
        }

        return $findings;
    }

    private function ensureEditable(Evaluation $evaluation): void
    {
        if (in_array($evaluation->status, [EvaluationStatus::Completed, EvaluationStatus::Archived], true)) {
            throw ValidationException::withMessages(['evaluation' => 'This evaluation can no longer be changed.']);
        }
        $evaluation->loadMissing('version.sections.questions', 'answers');
    }
}
