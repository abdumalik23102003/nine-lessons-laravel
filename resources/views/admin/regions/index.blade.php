@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Hududlar') }}</h2>
            <a href="{{ route('admin.regions.create') }}">
                <x-primary-button>{{ __('+ Yangi hudud') }}</x-primary-button>
            </a>
        </div>
    </x-slot>
    {{ Breadcrumbs::render('admin.regions.index') }}
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-red-600">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2">{{ __('Nomi') }}</th>
                        <th class="px-4 py-2">{{ __('Ota hudud') }}</th>
                        <th class="px-4 py-2">{{ __('Slug') }}</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($regions as $region)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $region->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $region->parent?->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $region->slug }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <a href="{{ route('admin.regions.edit', $region) }}"
                                   class="text-indigo-600 text-sm">{{ __('Tahrirlash') }}</a>
                                <form method="POST" action="{{ route('admin.regions.destroy', $region) }}"
                                      class="inline" onsubmit="return confirm('{{ __("O'chirilsinmi?") }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 text-sm ml-3">{{ __("O'chirish") }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">{{ __('Hududlar yo\'q.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $regions->links() }}</div>
        </div>
    </div>
</x-app-layout>
