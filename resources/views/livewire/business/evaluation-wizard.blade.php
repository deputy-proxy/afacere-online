<x-layouts::app :title="'Evaluation · '.$this->business->name">
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
            <h1 class="text-2xl font-semibold">Business evaluation</h1>
            <p class="mt-1 text-sm text-zinc-600">Answer honestly. Your answers are saved as you progress.</p>
        </div>

        @if ($this->section)
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span>Section {{ $sectionIndex + 1 }} of {{ $this->evaluation->version->sections->count() }}</span>
                    <span>{{ $this->evaluation->version->version }}</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-zinc-200">
                    <div class="h-full rounded-full bg-zinc-900 transition-all" style="width: {{ (($sectionIndex + 1) / max(1, $this->evaluation->version->sections->count())) * 100 }}%"></div>
                </div>
            </div>

            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-medium">{{ $this->section->title }}</h2>
                @if ($this->section->description)
                    <p class="mt-2 text-sm text-zinc-600">{{ $this->section->description }}</p>
                @endif

                @php($question = $this->section->questions->first())
                @if ($question)
                    <div class="mt-6">
                        <label for="answer" class="block text-sm font-medium">{{ $question->prompt }}</label>
                        @if (is_array($question->options) && count($question->options) > 0)
                            <select id="answer" wire:model="answer" class="mt-2 block w-full rounded-lg border-zinc-300">
                                <option value="">Select an answer</option>
                                @foreach ($question->options as $option)
                                    @if (is_array($option))
                                        <option value="{{ $option['value'] ?? $option['label'] ?? '' }}">{{ $option['label'] ?? $option['value'] ?? '' }}</option>
                                    @else
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <textarea id="answer" wire:model="answer" rows="6" class="mt-2 block w-full rounded-lg border-zinc-300" placeholder="Your answer..."></textarea>
                        @endif
                        @error('value') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('evaluation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="mt-6 flex items-center justify-between gap-3">
                    <button type="button" wire:click="previous" @disabled($sectionIndex === 0) class="rounded-lg border px-4 py-2 text-sm disabled:opacity-40">Previous</button>
                    @if ($sectionIndex + 1 === $this->evaluation->version->sections->count())
                        <button type="button" wire:click="complete" wire:loading.attr="disabled" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm text-white disabled:opacity-50">Complete evaluation</button>
                    @else
                        <button type="button" wire:click="saveAndNext" wire:loading.attr="disabled" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm text-white disabled:opacity-50">Save &amp; continue</button>
                    @endif
                </div>
            </section>
        @else
            <p class="rounded-lg border border-zinc-200 p-6 text-sm text-zinc-600">No evaluation sections are available.</p>
        @endif
    </div>
</x-layouts::app>
