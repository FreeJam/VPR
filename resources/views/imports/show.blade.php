<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $importBatch->original_filename }}</h2>
                <p class="mt-1 text-sm text-gray-500">Статус: {{ $importBatch->status }}</p>
            </div>
            <a href="{{ route('imports.preview', $importBatch) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Preview
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Questions</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900">{{ $preview['question_count'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Sections</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900">{{ $preview['section_count'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Manual</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900">{{ $preview['manual_question_count'] ?? 0 }}</p>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Errors</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900">{{ $importBatch->errors->count() }}</p>
                </div>
            </div>

            @if ($importBatch->errors->isNotEmpty())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-6 text-sm text-rose-800">
                    <h3 class="font-semibold">Ошибки валидации</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach ($importBatch->errors as $error)
                            <li>
                                <span class="font-medium">{{ $error->field_name ?: 'payload' }}:</span>
                                {{ $error->error_message }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($importBatch->assessmentLink?->assessment)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-6 text-sm text-emerald-800">
                    Импорт уже создал тест:
                    <a class="font-semibold underline" href="{{ route('assessments.show', $importBatch->assessmentLink->assessment) }}">
                        {{ $importBatch->assessmentLink->assessment->title }}
                    </a>
                </div>
            @elseif ($importBatch->status === 'validated')
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <form method="POST" action="{{ route('imports.run', $importBatch) }}" class="flex items-center justify-between gap-4">
                        @csrf
                        <input type="hidden" name="confirm" value="1">
                        <p class="text-sm text-gray-600">Файл валиден. Импорт создаст тест в статусе <code>draft</code>.</p>
                        <x-primary-button>Импортировать</x-primary-button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
