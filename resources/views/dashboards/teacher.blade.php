<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Кабинет учителя</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-5">
                @foreach ($stats as $label => $value)
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm uppercase tracking-wide text-gray-500">{{ str_replace('_', ' ', $label) }}</p>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <a href="{{ route('teacher.students.index') }}" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-sky-300">
                    <p class="text-sm uppercase tracking-wide text-gray-500">База</p>
                    <p class="mt-3 text-xl font-semibold text-gray-900">Мои ученики</p>
                </a>
                <a href="{{ route('teacher.groups.index') }}" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-sky-300">
                    <p class="text-sm uppercase tracking-wide text-gray-500">База</p>
                    <p class="mt-3 text-xl font-semibold text-gray-900">Мои группы</p>
                </a>
                <a href="{{ route('imports.create') }}" class="rounded-xl bg-sky-600 p-6 text-white shadow-sm transition hover:bg-sky-500">
                    <p class="text-sm uppercase tracking-wide text-sky-100">Шаг 1</p>
                    <p class="mt-3 text-xl font-semibold">Импортировать тест</p>
                </a>
                <a href="{{ route('assessments.index') }}" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-sky-300">
                    <p class="text-sm uppercase tracking-wide text-gray-500">Шаг 2</p>
                    <p class="mt-3 text-xl font-semibold text-gray-900">Открыть тест и создать назначение</p>
                </a>
                <a href="{{ route('reviews.index') }}" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 transition hover:ring-sky-300">
                    <p class="text-sm uppercase tracking-wide text-gray-500">Шаг 3</p>
                    <p class="mt-3 text-xl font-semibold text-gray-900">Проверить ручные задания</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
