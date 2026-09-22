<x-app-layout>

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
