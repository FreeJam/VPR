<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $group->name }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $group->gradeLevel?->name ?? 'Группа без привязки к классу' }}</p>
            </div>
            <a href="{{ route('teacher.groups.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Все группы</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-6 lg:grid-cols-[2fr,1fr]">
            <div>
                @include('teacher.groups.partials.form', [
                    'action' => route('teacher.groups.update', $group),
                    'method' => 'PATCH',
                    'submitLabel' => 'Сохранить изменения',
                ])
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Состав группы</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($group->members as $member)
                        <div class="rounded-lg border border-gray-200 p-4">
                            <p class="font-medium text-gray-900">{{ $member->studentProfile?->user?->name ?? 'Ученик' }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $member->studentProfile?->gradeLevel?->name ?? 'Класс не указан' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">В группе пока нет участников.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
