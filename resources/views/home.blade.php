<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Home') }}
            </h2>

            <nav class="flex items-center gap-4">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    >
                        Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-transparent rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    >
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                        >
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </x-slot>

    {{ Breadcrumbs::render('home') }}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('All Categories') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($categories as $category)
                        <div class="text-gray-700 dark:text-gray-300">{{ $category->name }}</div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ __('All Regions') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($regions as $region)
                        <div class="text-gray-700 dark:text-gray-300">{{ $region->name }}</div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
