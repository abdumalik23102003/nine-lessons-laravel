@php use Diglactic\Breadcrumbs\Breadcrumbs; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Sevimlilar') }}</h2>
    </x-slot>
    {{ Breadcrumbs::render('cabinet.favorites.index') }}
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @forelse ($adverts as $advert)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 flex items-center gap-4">
                    @if ($advert->photos->isNotEmpty())
                        <img src="{{ $advert->photos->first()->getFileUrl() }}"
                             class="h-16 w-16 object-cover rounded-md">
                    @else
                        <div class="h-16 w-16 bg-gray-100 dark:bg-gray-700 rounded-md"></div>
                    @endif

                    <div class="flex-1">
                        @if ($advert->isActive())
                            <a href="{{ route('adverts.show', $advert) }}"
                               class="font-medium text-gray-900 dark:text-gray-100 hover:underline">{{ $advert->title }}</a>
                        @else
                            <span class="font-medium text-gray-400">{{ $advert->title }}</span>
                            <span class="text-xs text-gray-400">({{ __('faol emas') }})</span>
                        @endif
                        <div class="text-sm text-gray-500">{{ $advert->price }} · {{ $advert->category->name }}</div>
                    </div>

                    <form method="POST" action="{{ route('cabinet.favorites.toggle', $advert) }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600">{{ __("Olib tashlash") }}</button>
                    </form>
                </div>
            @empty
                <div class="text-gray-500">{{ __("Hozircha sevimli e'lonlar yo'q.") }}</div>
            @endforelse

            <div>{{ $adverts->links() }}</div>
        </div>
    </div>
</x-app-layout>
