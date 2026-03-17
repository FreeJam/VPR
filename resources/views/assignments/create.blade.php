<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Новое назначение</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $version->assessment->title }} • {{ $version->assessment->subject->name }} • {{ $version->assessment->gradeLevel->name }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <p class="text-sm text-gray-600">
                    Версия {{ $version->version_label }} готова к раздаче ученику. Для MVP назначение создается напрямую
                    конкретному подключенному ученику.
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                @if ($students->isEmpty())
                    <p class="text-sm text-amber-700">
                        У вас пока нет подтвержденных учеников. Сначала свяжите ученика с учителем через `teacher_student_links`.
                    </p>
                @else
                    <form method="POST" action="{{ route('assignments.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="assessment_version_id" value="{{ $version->id }}">

                        <div>
                            <x-input-label for="student_profile_id" value="Ученик" />
                            <select id="student_profile_id" name="student_profile_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" @selected(old('student_profile_id') == $student->id)>
                                        {{ $student->user->name }}{{ $student->gradeLevel ? ' • '.$student->gradeLevel->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('student_profile_id')" />
                        </div>

                        <div>
                            <x-input-label for="title" value="Название назначения" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $version->assessment->title)" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="instructions" value="Инструкции" />
                            <textarea id="instructions" name="instructions" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('instructions') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('instructions')" />
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="mode" value="Режим" />
                                <select id="mode" name="mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                    @foreach (['training' => 'Тренировка', 'homework' => 'Домашняя работа', 'exam' => 'Экзамен'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('mode', 'training') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('mode')" />
                            </div>

                            <div>
                                <x-input-label for="max_attempts" value="Лимит попыток" />
                                <x-text-input id="max_attempts" name="max_attempts" type="number" min="1" max="10" class="mt-1 block w-full" :value="old('max_attempts', 1)" />
                                <x-input-error class="mt-2" :messages="$errors->get('max_attempts')" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label for="starts_at" value="Открыть с" />
                                <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full" :value="old('starts_at')" />
                                <x-input-error class="mt-2" :messages="$errors->get('starts_at')" />
                            </div>

                            <div>
                                <x-input-label for="due_at" value="Сдать до" />
                                <x-text-input id="due_at" name="due_at" type="datetime-local" class="mt-1 block w-full" :value="old('due_at')" />
                                <x-input-error class="mt-2" :messages="$errors->get('due_at')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('assessments.show', $version->assessment) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Назад к тесту
                            </a>
                            <x-primary-button>Создать назначение</x-primary-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
