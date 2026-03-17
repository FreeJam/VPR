<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Кабинет ученика</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                @foreach ($stats as $label => $value)
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm uppercase tracking-wide text-gray-500">{{ str_replace('_', ' ', $label) }}</p>
                        <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Как работать с платформой</h3>
                <ol class="mt-4 space-y-2 text-sm text-gray-600">
                    <li>1. Открой раздел «Назначения».</li>
                    <li>2. Запусти попытку и сохрани ответы.</li>
                    <li>3. Отправь работу и дождись ручной проверки, если она нужна.</li>
                </ol>
            </div>
        </div>
    </div>
</x-app-layout>
