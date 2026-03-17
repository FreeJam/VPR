<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Назначения</h2>
                <p class="mt-1 text-sm text-gray-500">
                    @if (Auth::user()->hasRole('teacher'))
                        Создавайте назначения из карточки теста и отслеживайте попытки учеников.
                    @else
                        Здесь собраны все доступные вам работы и текущий статус их выполнения.
                    @endif
                </p>
            </div>
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
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Тест</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">
                                    {{ Auth::user()->hasRole('teacher') ? 'Ученик' : 'Учитель' }}
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Срок</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($assignments as $assignment)
                                @php
                                    $latestAttempt = $assignment->attempts->sortByDesc('attempt_number')->first();
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $assignment->title ?: 'Без названия' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $assignment->assessmentVersion->assessment->title }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        @if (Auth::user()->hasRole('teacher'))
                                            {{ $assignment->studentProfile?->user?->name ?? 'Не указан' }}
                                        @else
                                            {{ $assignment->teacherProfile?->user?->name ?? 'Учитель' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $assignment->due_at?->format('d.m.Y H:i') ?? 'Без срока' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                {{ $assignment->status }}
                                            </span>
                                            @if ($latestAttempt)
                                                <div class="text-xs text-gray-500">
                                                    Попытка #{{ $latestAttempt->attempt_number }}: {{ $latestAttempt->status }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a class="font-medium text-sky-700 hover:text-sky-900" href="{{ route('assignments.show', $assignment) }}">
                                            Открыть
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">Назначений пока нет.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $assignments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
