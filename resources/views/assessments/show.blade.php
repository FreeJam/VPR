<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $assessment->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $assessment->subject->name }} • {{ $assessment->gradeLevel->name }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm text-gray-600">{{ $assessment->description ?: 'Описание пока не заполнено.' }}</p>
            </div>

            @foreach ($assessment->versions as $version)
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Версия {{ $version->version_label }}</h3>
                            <p class="text-sm text-gray-500">Статус: {{ $version->status }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                {{ $version->sections->sum(fn ($section) => $section->questions->count()) }} заданий
                            </span>
                            @if (Auth::user()->hasRole('teacher') && Auth::user()->can('view', $assessment))
                                <a href="{{ route('assignments.create', ['version' => $version->id]) }}" class="inline-flex items-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                                    Назначить
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($version->sections as $section)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <h4 class="font-semibold text-gray-900">{{ $section->position }}. {{ $section->title }}</h4>
                                <div class="mt-3 space-y-3">
                                    @foreach ($section->questions as $question)
                                        <div class="rounded-md bg-gray-50 p-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="font-medium text-gray-900">Задание {{ $question->external_number }}</p>
                                                <span class="text-xs text-gray-500">{{ $question->questionType->name }} • {{ $question->checking_mode }}</span>
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
            @endforeach
        </div>
    </div>
</x-app-layout>
