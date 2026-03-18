<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div>
            <x-input-label for="name" value="Название группы" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $group->name)" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="grade_level_id" value="Класс" />
                <select id="grade_level_id" name="grade_level_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    <option value="">Без ограничения</option>
                    @foreach ($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel->id }}" @selected((string) old('grade_level_id', $group->grade_level_id) === (string) $gradeLevel->id)>
                            {{ $gradeLevel->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('grade_level_id')" />
            </div>

            <div>
                <x-input-label for="description" value="Описание" />
                <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description', $group->description)" />
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>
        </div>

        <div>
            <x-input-label for="member_ids" value="Участники группы" />
            <div class="mt-3 grid gap-3 md:grid-cols-2">
                @forelse ($students as $student)
                    @php
                        $selected = collect(old('member_ids', $selectedMembers))->map(fn ($id) => (int) $id);
                    @endphp
                    <label class="flex items-start gap-3 rounded-lg border border-gray-200 px-4 py-3">
                        <input
                            type="checkbox"
                            name="member_ids[]"
                            value="{{ $student->id }}"
                            class="mt-1 rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500"
                            @checked($selected->contains($student->id))
                        >
                        <span class="text-sm text-gray-700">
                            <span class="block font-medium text-gray-900">{{ $student->user->name }}</span>
                            <span class="block text-gray-500">{{ $student->gradeLevel?->name ?? 'Класс не указан' }}</span>
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">Нет подключенных учеников для добавления в группу.</p>
                @endforelse
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('member_ids')" />
            <x-input-error class="mt-2" :messages="$errors->get('member_ids.*')" />
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('teacher.groups.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Отмена</a>
            <x-primary-button>{{ $submitLabel }}</x-primary-button>
        </div>
    </form>
</div>
