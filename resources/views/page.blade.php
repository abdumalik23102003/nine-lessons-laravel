@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $page->title }}</h2>
    </x-slot>

    {{ Breadcrumbs::render('pages.show', $page) }}

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div
                    class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $page->content }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
