@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Bannerni tahrirlash') }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('cabinet.banners.edit', $banner) }}
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div
                        class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div
                        class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-red-600">{{ session('error') }}</div>
            @endif

            <div
                    class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 text-sm text-gray-600 dark:text-gray-300 flex items-center justify-between">
                <span>{{ __('Status') }}: <strong>{{ \App\Models\Banner::statusesList()[$banner->status] ?? $banner->status }}</strong></span>
                @if ($banner->status === \App\Models\Banner::STATUS_ACTIVE)
                    <span>{{ __('Ko\'rishlar') }}: {{ $banner->views }} · {{ __('Bosishlar') }}: {{ $banner->clicks }}</span>
                @endif
            </div>

            @if ($banner->reject_reason)
                <div
                        class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3 text-sm text-red-700 dark:text-red-300">
                    <strong>{{ __('Rad etildi') }}:</strong> {{ $banner->reject_reason }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cabinet.banners.update', $banner) }}"
                      enctype="multipart/form-data">
                    @include('cabinet.banners._form')
                </form>
            </div>

            @if ($banner->isDraft() && $banner->file)
                <form method="POST" action="{{ route('cabinet.banners.send-to-moderation', $banner) }}">
                    @csrf
                    <x-secondary-button type="submit">{{ __('Moderatsiyaga yuborish') }}</x-secondary-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
