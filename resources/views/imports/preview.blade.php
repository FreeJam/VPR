<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Preview: {{ $importBatch->original_filename }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <dl class="grid gap-4 sm:grid-cols-2">
                    @foreach ($preview as $key => $value)
                        <div class="rounded-lg bg-gray-50 p-4">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">{{ str_replace('_', ' ', $key) }}</dt>
                            <dd class="mt-2 text-lg font-semibold text-gray-900">{{ is_bool($value) ? ($value ? 'yes' : 'no') : $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
