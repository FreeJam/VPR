<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Группы</h2>
                <p class="mt-1 text-sm text-gray-500">Учительские группы для массовых назначений и работы с классами.</p>
            </div>
            <a href="{{ route('teacher.groups.create') }}" class="inline-flex items-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                Новая группа
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Название</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Класс</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Участники</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($groups as $group)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $group->name }}</div>
                                        @if ($group->description)
                                            <div class="text-xs text-gray-500">{{ $group->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $group->gradeLevel?->name ?? 'Любой' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $group->members_count }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $group->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a class="font-medium text-sky-700 hover:text-sky-900" href="{{ route('teacher.groups.show', $group) }}">Открыть</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">Группы пока не созданы.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $groups->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
