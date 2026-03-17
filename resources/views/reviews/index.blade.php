<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Очередь проверки</h2>
            <p class="mt-1 text-sm text-gray-500">Ручная проверка заданий с `manual_open` и `manual_rubric`.</p>
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
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Тест</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Задание</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($reviews as $review)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-700">{{ $review->attempt->studentProfile?->user?->name ?? 'Ученик' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $review->attempt->assignment->assessmentVersion->assessment->title }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $review->question->external_number }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $review->reviewed_at ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $review->reviewed_at ? 'Проверено' : 'Ожидает проверки' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a class="font-medium text-sky-700 hover:text-sky-900" href="{{ route('reviews.show', $review) }}">
                                            Открыть
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">Заданий на ручную проверку пока нет.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
