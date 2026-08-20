<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Bannerlarim') }}</h2>
            <a href="{{ route('cabinet.banners.create') }}">
                <x-primary-button>{{ __('+ Yangi banner') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300">{{ session('status') }}</div>
            @endif

            @forelse ($banners as $banner)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex items-center gap-4">
                    @if ($banner->file)
                        <img src="{{ $banner->getFileUrl() }}" class="h-14 rounded" alt="">
                    @endif
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $banner->name }}</div>
                        <div class="text-sm text-gray-500">
                            {{ \App\Models\Banner::statusesList()[$banner->status] ?? $banner->status }}
                            @if ($banner->status === \App\Models\Banner::STATUS_ACTIVE)
                                · {{ __('Ko\'rishlar') }}: {{ $banner->views }} · {{ __('Bosishlar') }}: {{ $banner->clicks }}
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('cabinet.banners.edit', $banner) }}" class="text-indigo-600 text-sm">{{ __('Tahrirlash') }}</a>
                    <form method="POST" action="{{ route('cabinet.banners.destroy', $banner) }}" onsubmit="return confirm('{{ __("O'chirilsinmi?") }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 text-sm">{{ __("O'chirish") }}</button>
                    </form>
                </div>
            @empty
                <div class="text-gray-500">{{ __('Hali bannerlar yo\'q.') }}</div>
            @endforelse

            <div>{{ $banners->links() }}</div>
        </div>
    </div>
</x-app-layout>
