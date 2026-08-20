<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Mening e\'lonlarim') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <a href="{{ route('cabinet.adverts.create') }}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded-md">
                {{ __("Yangi e'lon") }}
            </a>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg divide-y">
                @forelse ($adverts as $advert)
                    <div class="p-4 flex justify-between items-center">
                        <div>
                            <a href="{{ route('cabinet.adverts.edit', $advert) }}" class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $advert->title }}
                            </a>
                            <div class="text-sm text-gray-500">{{ $advert->status }} · {{ $advert->price }}</div>
                        </div>
                        <form method="POST" action="{{ route('cabinet.adverts.destroy', $advert) }}" onsubmit="return confirm('O\'chirilsinmi?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-sm">{{ __("O'chirish") }}</button>
                        </form>
                    </div>
                @empty
                    <div class="p-4 text-gray-500">{{ __("Hali e'lonlar yo'q") }}</div>
                @endforelse
            </div>

            {{ $adverts->links() }}
        </div>
    </div>
</x-app-layout>
