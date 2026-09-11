<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EvaluationStatus;
use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationFinding;
use App\Models\EvaluationVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class EvaluationService
{
    public function create(Business $business, EvaluationVersion $version, User $user): Evaluation
    {
        abort_unless($user->businesses()->whereKey($business->id)->exists(), 403);

        return $business->evaluations()->create([
            'evaluation_version_id' => $version->id,
            'status' => EvaluationStatus::InProgress,
            'started_at' => now(),
        ]);
    }

    public function startOrResume(Business $business, ?User $user = null): Evaluation
    {
        $user ??= auth()->user();
        abort_unless($user instanceof User, 401);
        abort_unless($user->businesses()->whereKey($business->id)->exists(), 403);

        $existing = $business->evaluations()->whereIn('status', [EvaluationStatus::Draft->value, EvaluationStatus::InProgress->value])->latest('id')->first();
        if ($existing !== null) {
            return $existing->load('version.sections.questions', 'answers');
        }

        $version = EvaluationVersion::query()->where('is_active', true)->latest('id')->first();
        if ($version === null) {
            throw ValidationException::withMessages(['evaluation' => 'No active evaluation is available yet.']);
        }

        return $this->create($business, $version, $user)->load('version.sections.questions', 'answers');
    }

    public function saveAnswer(Evaluation $evaluation, string $questionKey, mixed $value, ?User $user = null): EvaluationAnswer
    {
        $user ??= auth()->user();
        abort_unless($user instanceof User, 401);
        $this->ensureEditable($evaluation, $user);
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
            ['value' => $value],
        );
    }

    /** @param array<string, mixed> $answers */
    public function saveAnswers(Evaluation $evaluation, User $user, array $answers): void
    {
        foreach ($answers as $questionKey => $value) {
            $this->saveAnswer($evaluation, $questionKey, $value, $user);
        }
    }

    public function complete(Evaluation $evaluation, ?User $user = null): Evaluation
    {
        $user ??= auth()->user();
        abort_unless($user instanceof User, 401);
        $this->ensureEditable($evaluation, $user);
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

    private function ensureEditable(Evaluation $evaluation, User $user): void
    {
        abort_unless($evaluation->business()->whereHas('members', fn ($query) => $query->whereKey($user->id))->exists(), 403);
        $status = $evaluation->getRawOriginal('status');
        if (is_string($status) && in_array($status, [EvaluationStatus::Completed->value, EvaluationStatus::Archived->value], true)) {
            throw ValidationException::withMessages(['evaluation' => 'This evaluation can no longer be changed.']);
        }
        $evaluation->loadMissing('version.sections.questions', 'answers');
    }
}
