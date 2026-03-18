<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Новая группа</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('teacher.groups.partials.form', [
                'action' => route('teacher.groups.store'),
                'method' => 'POST',
                'submitLabel' => 'Создать группу',
            ])
        </div>
    </div>
</x-app-layout>
