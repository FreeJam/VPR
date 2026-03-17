<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $assignment->title ?: 'Назначение' }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $assignment->assessmentVersion->assessment->title }} • {{ $assignment->assessmentVersion->assessment->subject->name }}
                </p>
            </div>

            @if (Auth::user()->hasRole('teacher'))
                <a href="{{ route('reviews.index') }}" class="inline-flex items-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                    Очередь проверки
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Статус</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $assignment->status }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Режим</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $assignment->mode }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Лимит попыток</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $assignment->max_attempts }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Срок</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $assignment->due_at?->format('d.m.Y H:i') ?? 'Без срока' }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[2fr,1fr]">
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">Структура работы</h3>
                        <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                            {{ $assignment->assessmentVersion->sections->sum(fn ($section) => $section->questions->count()) }} заданий
                        </span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($assignment->assessmentVersion->sections->sortBy('position') as $section)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <h4 class="font-semibold text-gray-900">{{ $section->position }}. {{ $section->title }}</h4>
                                <div class="mt-3 space-y-3">
                                    @foreach ($section->questions->sortBy('position') as $question)
                                        <div class="rounded-md bg-gray-50 p-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="font-medium text-gray-900">Задание {{ $question->external_number }}</p>
                                                <span class="text-xs text-gray-500">{{ $question->questionType->name }} • {{ $question->max_score }} балл.</span>
                                            </div>
                                            <div class="prose prose-sm mt-2 max-w-none text-gray-700">
                                                {!! $question->prompt_html !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Участники</h3>
                        <dl class="mt-4 space-y-3 text-sm text-gray-600">
                            <div>
                                <dt class="font-medium text-gray-900">Учитель</dt>
                                <dd>{{ $assignment->teacherProfile?->user?->name ?? 'Не указан' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-900">Ученик</dt>
                                <dd>{{ $assignment->studentProfile?->user?->name ?? 'Не указан' }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if (Auth::user()->hasRole('student'))
                        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Моя попытка</h3>

                            @if ($latestAttempt)
                                <div class="mt-4 space-y-3 text-sm text-gray-600">
                                    <p>Текущий статус: <span class="font-semibold text-gray-900">{{ $latestAttempt->status }}</span></p>
                                    <p>Номер попытки: {{ $latestAttempt->attempt_number }}</p>
                                    @if ($latestAttempt->submitted_at)
                                        <p>Отправлено: {{ $latestAttempt->submitted_at->format('d.m.Y H:i') }}</p>
                                    @endif
                                    @if ($latestAttempt->final_score > 0 || $latestAttempt->status === 'checked')
                                        <p>Итог: {{ number_format((float) $latestAttempt->final_score, 2, '.', ' ') }}{{ $latestAttempt->grade_label ? ' • оценка '.$latestAttempt->grade_label : '' }}</p>
                                    @endif
                                </div>

                                <div class="mt-5">
                                    <a href="{{ route('attempts.show', $latestAttempt) }}" class="inline-flex items-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                                        {{ $latestAttempt->status === 'in_progress' ? 'Продолжить попытку' : 'Открыть результат' }}
                                    </a>
                                </div>
                            @else
                                <div class="mt-4 space-y-4 text-sm text-gray-600">
                                    <p>Попытка еще не начата.</p>
                                    <form method="POST" action="{{ route('assignments.start', $assignment) }}">
                                        @csrf
                                        <x-primary-button>Начать попытку</x-primary-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Попытки ученика</h3>
                            <div class="mt-4 space-y-3">
                                @forelse ($assignment->attempts->sortByDesc('attempt_number') as $attempt)
                                    <div class="rounded-lg border border-gray-200 p-4 text-sm text-gray-600">
                                        <p class="font-medium text-gray-900">Попытка #{{ $attempt->attempt_number }}</p>
                                        <p class="mt-1">Статус: {{ $attempt->status }}</p>
                                        <p>Баллы: {{ number_format((float) $attempt->final_score, 2, '.', ' ') }}</p>
                                        @if ($attempt->grade_label)
                                            <p>Оценка: {{ $attempt->grade_label }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Ученик еще не начинал работу.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
