<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Yangi kategoriya') }}</h2>
    </x-slot>
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('admin.categories.create') }}
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @include('admin.categories._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
