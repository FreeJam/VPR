<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Кабинет родителя</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm uppercase tracking-wide text-gray-500">linked children</p>
                <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $stats['linked_children'] }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
