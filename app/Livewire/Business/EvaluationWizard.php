<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Evaluation;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\EvaluationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Evaluation')]
final class EvaluationWizard extends Component
{
    public ?int $evaluationId = null;

    public int $sectionIndex = 0;

    public string $answer = '';

    public function mount(BusinessContextService $businessContext, EvaluationService $evaluationService): void
    {
        $business = $businessContext->current($this->user());
        abort_unless($business !== null, 404);
        $evaluation = $evaluationService->startOrResume($business);
        $this->evaluationId = $evaluation->id;
        $this->sectionIndex = $this->firstIncompleteSection($evaluation);
        $this->loadCurrentAnswer($evaluation);
    }

    #[Computed]
    public function evaluation(): ?Evaluation
    {
        if ($this->evaluationId === null) {
            return null;
        }

        return Evaluation::query()->whereKey($this->evaluationId)->where('business_id', $this->business()->id)->with('version.sections.questions', 'answers')->first();
    }

    #[Computed]
    public function business(): Business
    {
        $business = app(BusinessContextService::class)->current($this->user());
        abort_unless($business !== null, 404);

        return $business;
    }

    #[Computed]
    public function section(): mixed
    {
        return $this->evaluation()?->version->sections->values()->get($this->sectionIndex);
    }

    public function saveAndNext(EvaluationService $evaluationService, NotificationService $notifications): void
    {
        $evaluation = $this->evaluation();
        $section = $this->section();
        abort_unless($evaluation !== null && $section !== null, 404);
        $question = $section->questions->first();
        abort_unless($question !== null, 422);
        $evaluationService->saveAnswer($evaluation, $question->key, $this->answer);

        if ($this->sectionIndex < $evaluation->version->sections->count() - 1) {
            $this->sectionIndex++;
            $this->answer = '';
            $this->loadCurrentAnswer($evaluation->fresh(['version.sections.questions', 'answers']));

            return;
        }

        $this->complete($evaluationService, $notifications);
    }

    public function previous(): void
    {
        if ($this->sectionIndex === 0) {
            return;
        }

        $this->sectionIndex--;
        $this->loadCurrentAnswer($this->evaluation());
    }

    public function complete(EvaluationService $evaluationService, NotificationService $notifications): void
    {
        $evaluation = $this->evaluation();
        $section = $this->section();
        abort_unless($evaluation !== null && $section !== null, 404);
        $question = $section->questions->first();
        abort_unless($question !== null, 422);
        $evaluationService->saveAnswer($evaluation, $question->key, $this->answer);
        $completed = $evaluationService->complete($evaluation);

        $notifications->recordEvent('evaluation.completed', $this->user(), $this->business(), $completed, ['version' => $completed->evaluation_version_id]);
        $notifications->notify($this->user(), 'evaluation.completed', 'Evaluation completed', 'Your diagnosis is ready to review.', $this->business(), [
            'event_key' => sprintf('evaluation:%d:completed', $completed->id),
            'url' => route('business.evaluation.diagnosis'),
        ]);
        $this->redirectRoute('business.evaluation.diagnosis');
    }

    private function firstIncompleteSection(Evaluation $evaluation): int
    {
        $answered = $evaluation->answers->pluck('question_key')->all();
        foreach ($evaluation->version->sections as $index => $section) {
            if ($section->questions->contains(fn ($question): bool => ! in_array($question->key, $answered, true))) {
                return $index;
            }
        }

        return max(0, $evaluation->version->sections->count() - 1);
    }

    private function loadCurrentAnswer(?Evaluation $evaluation): void
    {
        $question = $this->section()?->questions->first();
        $stored = $question !== null && $evaluation !== null ? $evaluation->answers->firstWhere('question_key', $question->key)?->getRawOriginal('value') : null;
        $decoded = is_string($stored) ? json_decode($stored, true) : null;
        $this->answer = is_array($decoded) && isset($decoded['answer']) && is_scalar($decoded['answer']) ? (string) $decoded['answer'] : '';
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.evaluation-wizard');
    }
}
