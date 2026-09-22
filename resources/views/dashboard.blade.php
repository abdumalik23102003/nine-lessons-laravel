<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    {{ Breadcrumbs::render('dashboard') }}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg p-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700 dark:text-gray-300">
                    {{ __('Xush kelibsiz') }}, <strong>{{ $user->name }}</strong>!
                </p>
            </div>

            {{-- USER stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <a href="{{ route('cabinet.adverts.index') }}"
                   class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-indigo-400 transition">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __("Mening e'lonlarim") }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['adverts_count'] }}</div>
                </a>
                <a href="{{ route('cabinet.favorites.index') }}"
                   class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-indigo-400 transition">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sevimlilar') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['favorites_count'] }}</div>
                </a>
                <a href="{{ route('cabinet.dialogs.index') }}"
                   class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-indigo-400 transition">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Ochiq dialoglar') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['open_dialogs'] }}</div>
                </a>
                <a href="{{ route('cabinet.tickets.index') }}"
                   class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-indigo-400 transition">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Murojaatlar') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['tickets_count'] }}</div>
                </a>
                <a href="{{ route('cabinet.banners.index') }}"
                   class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-indigo-400 transition">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Bannerlarim') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['banners_count'] }}</div>
                </a>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('cabinet.adverts.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    {{ __("+ Yangi e'lon") }}
                </a>
                <a href="{{ route('cabinet.banners.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                    {{ __('+ Yangi banner') }}
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Profil') }}
                </a>
            </div>

            {{-- MODERATOR / ADMIN --}}
            @if ($moderation)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Moderatsiya navbati') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <a href="{{ route('admin.moderation.index') }}"
                           class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-amber-400 transition">
                            <div class="text-sm text-amber-700 dark:text-amber-300">{{ __("Kutayotgan e'lonlar") }}</div>
                            <div class="mt-1 text-2xl font-semibold text-amber-900 dark:text-amber-100">{{ $moderation['adverts_on_moderation'] }}</div>
                        </a>
                        <a href="{{ route('admin.banners.moderation.index') }}"
                           class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-amber-400 transition">
                            <div class="text-sm text-amber-700 dark:text-amber-300">{{ __('Kutayotgan bannerlar') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-amber-900 dark:text-amber-100">{{ $moderation['banners_on_moderation'] }}</div>
                        </a>
                        <a href="{{ route('admin.tickets.index') }}"
                           class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 shadow-sm sm:rounded-lg p-5 hover:ring-2 hover:ring-amber-400 transition">
                            <div class="text-sm text-amber-700 dark:text-amber-300">{{ __('Ochiq murojaatlar') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-amber-900 dark:text-amber-100">{{ $moderation['open_tickets'] }}</div>
                        </a>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('admin.categories.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Kategoriyalar') }}</a>
                        <a href="{{ route('admin.pages.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Sahifalar') }}</a>
                        @if ($user->isAdmin())
                            <a href="{{ route('admin.regions.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Hududlar') }}</a>
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:underline">{{ __('Foydalanuvchilar') }}</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
