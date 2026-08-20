<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $advert->title }}</h2>
            @auth
                <form method="POST" action="{{ route('cabinet.favorites.toggle', $advert) }}">
                    @csrf
                    @php $isFavorited = auth()->user()->hasFavorited($advert); @endphp
                    <button type="submit" class="text-sm px-3 py-1.5 rounded-md border {{ $isFavorited ? 'bg-red-50 border-red-200 text-red-600 dark:bg-red-900/20 dark:border-red-800' : 'border-gray-300 text-gray-600 dark:text-gray-300' }}">
                        {{ $isFavorited ? '♥ ' . __('Sevimlilarda') : '♡ ' . __('Sevimlilarga qo\'shish') }}
                    </button>
                </form>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="grid grid-cols-4 gap-2">
                @forelse ($advert->photos as $photo)
                    <img src="{{ $photo->getFileUrl() }}" class="rounded-md w-full h-32 object-cover">
                @empty
                    <div class="col-span-4 h-40 bg-gray-100 dark:bg-gray-700 rounded-md"></div>
                @endforelse
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="text-2xl font-semibold mb-2">{{ $advert->price }}</div>
                <div class="text-sm text-gray-500 mb-4">
                    {{ $advert->category->name }}
                    @if ($advert->region) · {{ $advert->region->getAddress() }} @endif
                </div>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $advert->content }}</p>

                @if ($advert->values->isNotEmpty())
                    <dl class="mt-4 grid grid-cols-2 gap-2">
                        @foreach ($advert->values as $value)
                            <dt class="text-sm text-gray-500">{{ $value->attribute->name }}</dt>
                            <dd class="text-sm">{{ $value->value }}</dd>
                        @endforeach
                    </dl>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
