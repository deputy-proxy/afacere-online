<?php

namespace App\Services;

use App\Enums\EvaluationStatus;
use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationAnswer;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationVersion;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class EvaluationService
{
    public function create(Business $business, EvaluationVersion $version, User $actor): Evaluation
    {
        Gate::forUser($actor)->authorize('view', $business);

        return $business->evaluations()->create([
            'evaluation_version_id' => $version->id,
            'status' => EvaluationStatus::Draft,
        ]);
    }

    /** @param array<string, mixed> $answers */
    public function saveAnswers(Evaluation $evaluation, User $actor, array $answers): Evaluation
    {
        $business = $evaluation->business;
        Gate::forUser($actor)->authorize('view', $business);

        if ($evaluation->status === EvaluationStatus::Completed || $evaluation->status === EvaluationStatus::Archived) {
            throw ValidationException::withMessages(['evaluation' => 'Completed evaluations cannot be changed.']);
        }

        $questions = $evaluation->version->sections()->with('questions')->get()->flatMap->questions->keyBy('key');

        foreach ($answers as $key => $value) {
            $question = $questions->get($key);
            if (! $question instanceof EvaluationQuestion) {
                throw ValidationException::withMessages(["answers.$key" => 'The question does not belong to the pinned evaluation version.']);
            }

            $rules = $question->validation_rules ?? [];
            if ($question->required && ! array_key_exists('required', $rules)) {
                $rules['required'] = true;
            }
            Validator::make(['value' => $value], ['value' => $rules])->validate();

            EvaluationAnswer::updateOrCreate(
                ['evaluation_id' => $evaluation->id, 'question_key' => $key],
                ['value' => $value],
            );
        }

        $evaluation->update([
            'status' => EvaluationStatus::InProgress,
            'started_at' => $evaluation->started_at ?? now(),
        ]);

        return $evaluation->fresh(['answers']) ?? $evaluation;
    }

    public function complete(Evaluation $evaluation, User $actor): Evaluation
    {
        $business = $evaluation->business;
        Gate::forUser($actor)->authorize('view', $business);

        $requiredKeys = $evaluation->version->sections()->with('questions')->get()
            ->flatMap->questions->filter(fn (EvaluationQuestion $question): bool => $question->required)
            ->pluck('key');
        $answeredKeys = $evaluation->answers()->pluck('question_key');

        if ($requiredKeys->diff($answeredKeys)->isNotEmpty()) {
            throw ValidationException::withMessages(['answers' => 'All required evaluation questions must be answered.']);
        }

        $evaluation->update([
            'status' => EvaluationStatus::Completed,
            'started_at' => $evaluation->started_at ?? now(),
            'completed_at' => now(),
        ]);

        return $evaluation->fresh() ?? $evaluation;
    }
}
