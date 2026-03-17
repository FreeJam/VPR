<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Загрузка импортного файла</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <form method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="file" :value="__('JSON файл')" />
                        <input id="file" name="file" type="file" accept=".json,.txt" class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>

                    <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                        Поддерживается формат импорта <code>v1.0</code>. Неизвестные поля не блокируют импорт, но обязательные поля будут провалидированы.
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Загрузить и проверить</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
