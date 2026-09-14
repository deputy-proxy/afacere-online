<div class="mx-auto max-w-4xl space-y-6">
    <header>
        <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
        <h1 class="text-2xl font-semibold">Business evaluation</h1>
        <p class="mt-1 text-sm text-zinc-600">Your answers are saved as you progress. You can revisit completed sections before finishing.</p>
    </header>
    @if ($this->evaluation && $this->evaluation->version->sections->isNotEmpty())
        <nav aria-label="Evaluation sections" class="overflow-x-auto">
            <ol class="flex min-w-max gap-2">
                @foreach ($this->evaluation->version->sections as $index => $item)
                    @php($answered = $item->questions->every(fn ($itemQuestion) => $this->evaluation->answers->contains('question_key', $itemQuestion->key)))
                    <li>
                        <button type="button" wire:click="goToSection({{ $index }})" wire:key="evaluation-section-{{ $item->id }}" aria-current="{{ $sectionIndex === $index ? 'step' : 'false' }}" class="rounded-lg border px-3 py-2 text-sm {{ $sectionIndex === $index ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-200 bg-white text-zinc-700' }}">
                            <span>{{ $index + 1 }}. {{ $item->title }}</span>
                            @if ($answered)<span class="sr-only">completed</span>@endif
                        </button>
                    </li>
                @endforeach
            </ol>
        </nav>
        <div aria-label="Evaluation progress" class="space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span>Question {{ $this->currentQuestionNumber }} of {{ $this->totalQuestions }}</span>
                <span>Version {{ $this->evaluation->version->version }}</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-zinc-200" role="progressbar" aria-valuemin="1" aria-valuemax="{{ max(1, $this->totalQuestions) }}" aria-valuenow="{{ $this->currentQuestionNumber }}" aria-label="Evaluation progress">
                <div class="h-full rounded-full bg-zinc-900 transition-all" style="width: {{ ($this->currentQuestionNumber / max(1, $this->totalQuestions)) * 100 }}%"></div>
            </div>
        </div>
        @if ($this->question)
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm" aria-labelledby="evaluation-question">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ $this->section->title }}</p>
                <h2 id="evaluation-question" class="mt-2 text-xl font-medium">{{ $this->question->prompt }}</h2>
                <div class="mt-6">
                    @php($type = strtolower($this->question->type))
                    @php($options = is_array($this->question->options) ? $this->question->options : [])
                    @if (in_array($type, ['select', 'dropdown'], true))
                        <label for="answer" class="sr-only">{{ $this->question->prompt }}</label>
                        <select id="answer" wire:model="answer" class="block w-full rounded-lg border-zinc-300">
                            <option value="">Select an answer</option>
                            @foreach ($options as $option)
                                @php($value = is_array($option) ? ($option['value'] ?? $option['label'] ?? '') : $option)
                                @php($label = is_array($option) ? ($option['label'] ?? $option['value'] ?? '') : $option)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    @elseif (in_array($type, ['radio', 'choice'], true))
                        <fieldset class="space-y-3"><legend class="sr-only">{{ $this->question->prompt }}</legend>
                            @foreach ($options as $option)
                                @php($value = is_array($option) ? ($option['value'] ?? $option['label'] ?? '') : $option)
                                @php($label = is_array($option) ? ($option['label'] ?? $option['value'] ?? '') : $option)
                                <label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-3"><input type="radio" wire:model="answer" value="{{ $value }}" class="border-zinc-300"><span>{{ $label }}</span></label>
                            @endforeach
                        </fieldset>
                    @elseif (in_array($type, ['checkbox', 'multiselect'], true))
                        <fieldset class="space-y-3"><legend class="sr-only">{{ $this->question->prompt }}</legend>
                            @foreach ($options as $option)
                                @php($value = is_array($option) ? ($option['value'] ?? $option['label'] ?? '') : $option)
                                @php($label = is_array($option) ? ($option['label'] ?? $option['value'] ?? '') : $option)
                                <label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-3"><input type="checkbox" wire:model="answer" value="{{ $value }}" class="rounded border-zinc-300"><span>{{ $label }}</span></label>
                            @endforeach
                        </fieldset>
                    @elseif (in_array($type, ['boolean', 'bool', 'yes_no'], true))
                        <fieldset><legend class="sr-only">{{ $this->question->prompt }}</legend><div class="grid gap-3 sm:grid-cols-2">
                            <label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-3"><input type="radio" wire:model="answer" value="yes" class="border-zinc-300"><span>Yes</span></label>
                            <label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-3"><input type="radio" wire:model="answer" value="no" class="border-zinc-300"><span>No</span></label>
                        </div></fieldset>
                    @elseif (in_array($type, ['number', 'integer', 'decimal'], true))
                        <label for="answer" class="sr-only">{{ $this->question->prompt }}</label><input id="answer" type="number" wire:model="answer" inputmode="decimal" step="any" class="block w-full rounded-lg border-zinc-300" placeholder="Your answer">
                    @elseif (in_array($type, ['date', 'datetime'], true))
                        <label for="answer" class="sr-only">{{ $this->question->prompt }}</label><input id="answer" type="{{ $type === 'datetime' ? 'datetime-local' : 'date' }}" wire:model="answer" class="block w-full rounded-lg border-zinc-300">
                    @elseif (in_array($type, ['email', 'url'], true))
                        <label for="answer" class="sr-only">{{ $this->question->prompt }}</label><input id="answer" type="{{ $type }}" wire:model="answer" class="block w-full rounded-lg border-zinc-300" placeholder="Your answer">
                    @elseif (in_array($type, ['textarea', 'long_text', 'longtext'], true))
                        <label for="answer" class="sr-only">{{ $this->question->prompt }}</label><textarea id="answer" wire:model="answer" rows="6" class="block w-full rounded-lg border-zinc-300" placeholder="Your answer"></textarea>
                    @else
                        <label for="answer" class="sr-only">{{ $this->question->prompt }}</label><input id="answer" type="text" wire:model="answer" class="block w-full rounded-lg border-zinc-300" placeholder="Your answer">
                    @endif
                    @error('value')<p id="answer-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                    @error('answer')<p id="answer-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                    @error('evaluation')<p id="evaluation-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <button type="button" wire:click="previous" @disabled($this->currentQuestionNumber <= 1) class="rounded-lg border px-4 py-2 text-sm disabled:opacity-40">Previous</button>
                    <button type="button" wire:click="{{ $this->isLastQuestion ? 'complete' : 'saveAndNext' }}" wire:loading.attr="disabled" wire:target="saveAndNext,complete" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm text-white disabled:opacity-50">
                        <span wire:loading.remove wire:target="saveAndNext,complete">{{ $this->isLastQuestion ? 'Complete evaluation' : 'Save &amp; continue' }}</span>
                        <span wire:loading wire:target="saveAndNext,complete">Saving…</span>
                    </button>
                </div>
            </section>
        @else
            <p class="rounded-lg border border-zinc-200 p-6 text-sm text-zinc-600">No question is available in this section.</p>
        @endif
    @else
        <p class="rounded-lg border border-zinc-200 p-6 text-sm text-zinc-600">No evaluation sections are available.</p>
    @endif
</div>
