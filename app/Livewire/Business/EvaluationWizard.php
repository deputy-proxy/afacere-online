<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\EvaluationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Evaluation')]
final class EvaluationWizard extends Component
{
    public ?int $evaluationId = null;

    public int $sectionIndex = 0;

    public int $questionIndex = 0;

    public mixed $answer = '';

    public function mount(BusinessContextService $businessContext, EvaluationService $evaluationService): void
    {
        $business = $businessContext->current($this->user());
        abort_unless($business !== null, 404);
        $evaluation = $evaluationService->startOrResume($business);
        $this->evaluationId = $evaluation->id;
        [$this->sectionIndex, $this->questionIndex] = $this->firstIncompleteQuestion($evaluation);
        $this->loadCurrentAnswer($evaluation);
    }

    #[Computed]
    public function evaluation(): ?Evaluation
    {
        if ($this->evaluationId === null) {
            return null;
        }

        return Evaluation::query()
            ->whereKey($this->evaluationId)
            ->where('business_id', $this->business()->id)
            ->with('version.sections.questions', 'answers')
            ->first();
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

    #[Computed]
    public function question(): ?EvaluationQuestion
    {
        return $this->section()?->questions->values()->get($this->questionIndex);
    }

    #[Computed]
    public function totalQuestions(): int
    {
        return $this->evaluation()?->version->sections->sum(fn ($section): int => $section->questions->count()) ?? 0;
    }

    #[Computed]
    public function currentQuestionNumber(): int
    {
        $evaluation = $this->evaluation();
        if ($evaluation === null) {
            return 0;
        }

        $before = $evaluation->version->sections->take($this->sectionIndex)->sum(fn ($section): int => $section->questions->count());

        return $before + $this->questionIndex + 1;
    }

    #[Computed]
    public function isLastQuestion(): bool
    {
        return $this->currentQuestionNumber() === $this->totalQuestions();
    }

    public function saveAndNext(EvaluationService $evaluationService, NotificationService $notifications): void
    {
        $evaluation = $this->evaluation();
        $question = $this->question();
        abort_unless($evaluation !== null && $question !== null, 404);
        $evaluationService->saveAnswer($evaluation, $question->key, $this->answer, $this->user());

        if (! $this->isLastQuestion()) {
            $this->advance();
            $this->loadCurrentAnswer($evaluation->fresh(['version.sections.questions', 'answers']));

            return;
        }

        $this->complete($evaluationService, $notifications);
    }

    public function previous(): void
    {
        if ($this->currentQuestionNumber() <= 1) {
            return;
        }

        if ($this->questionIndex > 0) {
            $this->questionIndex--;
        } else {
            $this->sectionIndex--;
            $this->questionIndex = (int) max(0, $this->section()?->questions->count() - 1);
        }

        $this->loadCurrentAnswer($this->evaluation());
    }

    public function goToSection(int $index): void
    {
        $evaluation = $this->evaluation();
        abort_unless($evaluation !== null, 404);
        abort_unless($index >= 0 && $index < $evaluation->version->sections->count(), 404);

        $this->sectionIndex = $index;
        $this->questionIndex = 0;
        $this->loadCurrentAnswer($evaluation);
    }

    public function complete(EvaluationService $evaluationService, NotificationService $notifications): void
    {
        $evaluation = $this->evaluation();
        $question = $this->question();
        abort_unless($evaluation !== null && $question !== null, 404);
        $evaluationService->saveAnswer($evaluation, $question->key, $this->answer, $this->user());
        $completed = $evaluationService->complete($evaluation, $this->user());

        $notifications->recordEvent('evaluation.completed', $this->user(), $this->business(), $completed, ['version' => $completed->evaluation_version_id]);
        $notifications->notify($this->user(), 'evaluation.completed', 'Evaluation completed', 'Your diagnosis is ready to review.', $this->business(), [
            'event_key' => sprintf('evaluation:%d:completed', $completed->id),
            'url' => route('business.evaluation.diagnosis'),
        ]);
        $this->redirectRoute('business.evaluation.diagnosis');
    }

    /** @return array{0: int, 1: int} */
    private function firstIncompleteQuestion(Evaluation $evaluation): array
    {
        $answered = $evaluation->answers->pluck('question_key')->all();
        foreach ($evaluation->version->sections as $sectionIndex => $section) {
            foreach ($section->questions as $questionIndex => $question) {
                if (! in_array($question->key, $answered, true)) {
                    return [$sectionIndex, $questionIndex];
                }
            }
        }

        return [
            max(0, $evaluation->version->sections->count() - 1),
            (int) max(0, ($evaluation->version->sections->last()?->questions->count() ?? 0) - 1),
        ];
    }

    private function advance(): void
    {
        $evaluation = $this->evaluation();
        if ($evaluation === null) {
            return;
        }

        $section = $evaluation->version->sections->values()->get($this->sectionIndex);
        if ($section === null) {
            return;
        }

        if ($this->questionIndex + 1 < $section->questions->count()) {
            $this->questionIndex++;

            return;
        }

        $this->sectionIndex++;
        $this->questionIndex = 0;
    }

    private function loadCurrentAnswer(?Evaluation $evaluation): void
    {
        $question = $this->question();
        $this->answer = $question !== null && $evaluation !== null
            ? ($evaluation->answers->firstWhere('question_key', $question->key)?->value ?? '')
            : '';
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
