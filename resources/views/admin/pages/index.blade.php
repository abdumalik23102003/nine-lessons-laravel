<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Sahifalar') }}</h2>
            <a href="{{ route('admin.pages.create') }}">
                <x-primary-button>{{ __('+ Yangi sahifa') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2">{{ __('Sarlavha') }}</th>
                        <th class="px-4 py-2">{{ __('Slug') }}</th>
                        <th class="px-4 py-2">{{ __('Menyuda') }}</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($pages as $page)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $page->title }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $page->slug }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $page->show_in_menu ? __('Ha') : __('Yo\'q') }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <a href="{{ route('pages.show', $page) }}" target="_blank" class="text-gray-500 text-sm">{{ __('Ko\'rish') }}</a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-indigo-600 text-sm ml-3">{{ __('Tahrirlash') }}</a>
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="inline" onsubmit="return confirm('{{ __("O'chirilsinmi?") }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 text-sm ml-3">{{ __("O'chirish") }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">{{ __('Sahifalar yo\'q.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $pages->links() }}</div>
        </div>
    </div>
</x-app-layout>
