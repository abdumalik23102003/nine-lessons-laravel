@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
{{-- resources/views/cabinet/adverts/create.blade.php --}}
<x-app-layout>

    {{Breadcrumbs::render('adverts.create')}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __("Yangi e'lon") }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cabinet.adverts.store') }}">
                    @include('cabinet.adverts._form', ['advert' => null])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
