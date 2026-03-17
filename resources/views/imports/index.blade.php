<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Импорт JSON</h2>
            <a href="{{ route('imports.create') }}" class="inline-flex items-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                Новый импорт
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
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Файл</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Ошибки</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Действие</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($imports as $import)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-700">{{ $import->original_filename }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">{{ $import->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $import->errors->count() }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('imports.show', $import) }}" class="font-medium text-sky-700 hover:text-sky-900">Открыть</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Импортов пока нет.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $imports->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
