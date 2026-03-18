<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Мои ученики</h2>
                <p class="mt-1 text-sm text-gray-500">Подтвержденные связи учитель -> ученик, доступные для назначений и групп.</p>
            </div>
            <a href="{{ route('teacher.groups.create') }}" class="inline-flex items-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                Создать группу
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
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Ученик</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Класс</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Группы</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Назначения</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Подключен</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($students as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $item['student']?->user?->name ?? 'Ученик' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item['student']?->user?->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['student']?->gradeLevel?->name ?? 'Не указан' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['groups_count'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['assignments_count'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['link']->approved_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">Подключенных учеников пока нет.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
