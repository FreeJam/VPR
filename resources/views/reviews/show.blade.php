<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Проверка задания {{ $review->question->external_number }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $review->attempt->studentProfile?->user?->name ?? 'Ученик' }} • {{ $review->attempt->assignment->assessmentVersion->assessment->title }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Статус попытки</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $review->attempt->status }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Текущий итог</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ number_format((float) $review->attempt->final_score, 2, '.', ' ') }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Ручной балл</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ number_format((float) $review->awarded_score, 2, '.', ' ') }}</p>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Формулировка задания</h3>
                <div class="prose prose-sm mt-4 max-w-none text-gray-700">
                    {!! $review->question->prompt_html !!}
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Ответ ученика</h3>
                <div class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-700 whitespace-pre-wrap">
                    @if ($response?->response_text)
                        {{ $response->response_text }}
                    @elseif ($response?->response_json)
                        {{ json_encode($response->response_json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
                    @else
                        Ответ не заполнен.
                    @endif
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <form method="POST" action="{{ route('reviews.update', $review) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-5">
                        @forelse ($review->question->rubric?->criteria ?? [] as $criterion)
                            @php
                                $currentScore = old("scores.{$criterion->id}", $review->criterionScores->firstWhere('rubric_criterion_id', $criterion->id)?->points ?? 0);
                            @endphp
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $criterion->code }}. {{ $criterion->title }}</h4>
                                        @if ($criterion->description)
                                            <p class="mt-1 text-sm text-gray-500">{{ $criterion->description }}</p>
                                        @endif
                                    </div>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                        max {{ $criterion->max_points }}
                                    </span>
                                </div>

                                <div class="mt-4">
                                    @if ($criterion->levels->isNotEmpty())
                                        <select name="scores[{{ $criterion->id }}]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                            @foreach ($criterion->levels->sortByDesc('points') as $level)
                                                <option value="{{ $level->points }}" @selected((float) $currentScore === (float) $level->points)>
                                                    {{ $level->points }} — {{ $level->description ?: 'Уровень' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <x-text-input
                                            :id="'criterion_'.$criterion->id"
                                            :name="'scores['.$criterion->id.']'"
                                            type="number"
                                            step="0.5"
                                            min="0"
                                            :max="(string) $criterion->max_points"
                                            class="block w-full"
                                            :value="(string) $currentScore"
                                        />
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                Для этого задания rubric не найден. Можно сохранить только комментарий.
                            </div>
                        @endforelse
                    </div>

                    <div>
                        <x-input-label for="comment" value="Комментарий ученику" />
                        <textarea id="comment" name="comment" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('comment', $review->comment) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('comment')" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('reviews.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Назад к очереди
                        </a>
                        <x-primary-button>Сохранить проверку</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
