<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Попытка #{{ $attempt->attempt_number }}: {{ $attempt->assignment->title }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $attempt->assignment->assessmentVersion->assessment->title }} • статус {{ $attempt->status }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Авто</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format((float) $attempt->auto_score, 2, '.', ' ') }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Ручная проверка</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format((float) $attempt->manual_score, 2, '.', ' ') }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Итог</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format((float) $attempt->final_score, 2, '.', ' ') }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Оценка</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $attempt->grade_label ?? '—' }}</p>
                </div>
            </div>

            @if ($errors->has('assignment'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('assignment') }}
                </div>
            @endif

            <form method="POST" action="{{ route('attempts.update', $attempt) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                @foreach ($attempt->assignment->assessmentVersion->sections->sortBy('position') as $section)
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $section->position }}. {{ $section->title }}</h3>
                                @if ($section->instruction_html)
                                    <div class="prose prose-sm mt-2 max-w-none text-gray-700">
                                        {!! $section->instruction_html !!}
                                    </div>
                                @endif
                            </div>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                {{ $section->questions->count() }} заданий
                            </span>
                        </div>

                        <div class="mt-6 space-y-6">
                            @foreach ($section->questions->sortBy('position') as $question)
                                @php
                                    $response = $responses->get($question->id);
                                    $storedValue = old("answers.{$question->id}", $response?->response_json ?? $response?->response_text);
                                    $typeCode = $question->questionType->code;
                                    $structuredParts = $question->response_structure_json['parts'] ?? [];
                                    $review = $attempt->questionReviews->firstWhere('question_id', $question->id);
                                @endphp

                                <div class="rounded-lg border border-gray-200 p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold uppercase tracking-wide text-sky-700">Задание {{ $question->external_number }}</p>
                                            <p class="mt-1 text-sm text-gray-500">{{ $question->questionType->name }} • {{ $question->checking_mode }}</p>
                                        </div>
                                        <div class="text-right text-sm text-gray-500">
                                            <div>Макс. балл: {{ $question->max_score }}</div>
                                            @if ($response)
                                                <div>Авто: {{ number_format((float) $response->auto_score, 2, '.', ' ') }}</div>
                                            @endif
                                            @if ($review)
                                                <div>Ручная: {{ number_format((float) $review->awarded_score, 2, '.', ' ') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="prose prose-sm mt-4 max-w-none text-gray-700">
                                        {!! $question->prompt_html !!}
                                    </div>

                                    @if ($question->instruction_html)
                                        <div class="mt-3 rounded-md bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                            {!! $question->instruction_html !!}
                                        </div>
                                    @endif

                                    <div class="mt-5">
                                        @if ($typeCode === 'single_choice')
                                            <div class="space-y-3">
                                                @foreach ($question->options->sortBy('position') as $option)
                                                    <label class="flex items-start gap-3 rounded-md border border-gray-200 px-4 py-3">
                                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->option_key }}" class="mt-1" @checked($storedValue === $option->option_key) @disabled(! $canEdit)>
                                                        <span class="text-sm text-gray-700">{{ $option->option_key }}. {{ $option->text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif ($typeCode === 'multiple_choice')
                                            @php
                                                $selected = collect(is_array($storedValue) ? $storedValue : []);
                                            @endphp
                                            <div class="space-y-3">
                                                @foreach ($question->options->sortBy('position') as $option)
                                                    <label class="flex items-start gap-3 rounded-md border border-gray-200 px-4 py-3">
                                                        <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->option_key }}" class="mt-1" @checked($selected->contains($option->option_key)) @disabled(! $canEdit)>
                                                        <span class="text-sm text-gray-700">{{ $option->option_key }}. {{ $option->text }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif ($typeCode === 'multi_field_text')
                                            <textarea
                                                name="answers[{{ $question->id }}]"
                                                rows="4"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                                placeholder="Каждый вариант с новой строки"
                                                @disabled(! $canEdit)
                                            >{{ is_array($storedValue) ? implode(PHP_EOL, $storedValue) : (string) $storedValue }}</textarea>
                                        @elseif (! empty($structuredParts))
                                            <div class="space-y-4">
                                                @foreach ($structuredParts as $part)
                                                    @php
                                                        $partValue = old(
                                                            "answers.{$question->id}.{$part['code']}",
                                                            is_array($storedValue) ? ($storedValue[$part['code']] ?? '') : ''
                                                        );
                                                    @endphp
                                                    <div>
                                                        <x-input-label :for="'question_'.$question->id.'_'.$part['code']" :value="$part['label'] ?? $part['code']" />
                                                        @if (str_contains($part['type'] ?? '', 'open'))
                                                            <textarea
                                                                id="question_{{ $question->id }}_{{ $part['code'] }}"
                                                                name="answers[{{ $question->id }}][{{ $part['code'] }}]"
                                                                rows="3"
                                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                                                @disabled(! $canEdit)
                                                            >{{ $partValue }}</textarea>
                                                        @else
                                                            <x-text-input
                                                                :id="'question_'.$question->id.'_'.$part['code']"
                                                                :name="'answers['.$question->id.']['.$part['code'].']'"
                                                                type="text"
                                                                class="mt-1 block w-full"
                                                                :value="$partValue"
                                                                :disabled="! $canEdit"
                                                            />
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($typeCode === 'numeric')
                                            <x-text-input
                                                :id="'question_'.$question->id"
                                                :name="'answers['.$question->id.']'"
                                                type="number"
                                                step="any"
                                                class="block w-full"
                                                :value="(string) $storedValue"
                                                :disabled="! $canEdit"
                                            />
                                        @elseif ($canEdit)
                                            <textarea
                                                name="answers[{{ $question->id }}]"
                                                rows="5"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
                                            >{{ is_array($storedValue) ? json_encode($storedValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : (string) $storedValue }}</textarea>
                                        @else
                                            <div class="rounded-md bg-gray-50 px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap">
                                                @if (is_array($storedValue))
                                                    {{ json_encode($storedValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
                                                @else
                                                    {{ $storedValue !== null && $storedValue !== '' ? $storedValue : 'Ответ не заполнен.' }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    @if ($review?->comment)
                                        <div class="mt-4 rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                            Комментарий учителя: {{ $review->comment }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if ($canEdit)
                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <a href="{{ route('assignments.show', $attempt->assignment) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Назад к назначению
                        </a>
                        <x-secondary-button>Сохранить черновик</x-secondary-button>
                        <x-primary-button formaction="{{ route('attempts.submit', $attempt) }}">Отправить работу</x-primary-button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-app-layout>
