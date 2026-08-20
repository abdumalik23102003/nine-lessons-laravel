<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __("E'lonlar") }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <form method="GET" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 mb-4 flex flex-wrap gap-3">
                <input type="text" name="text" value="{{ request('text') }}" placeholder="{{ __('Qidirish...') }}" class="border-gray-300 rounded-md flex-1 min-w-[200px]">

                <select name="category_id" class="border-gray-300 rounded-md">
                    <option value="">{{ __('Barcha kategoriyalar') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category['id'] }}" @selected(request('category_id') == $category['id'])>{{ $category['name'] }}</option>
                    @endforeach
                </select>

                <select name="region_id" class="border-gray-300 rounded-md">
                    <option value="">{{ __('Barcha hududlar') }}</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region['id'] }}" @selected(request('region_id') == $region['id'])>{{ $region['name'] }}</option>
                    @endforeach
                </select>

                <input type="number" name="price_from" value="{{ request('price_from') }}" placeholder="{{ __('Narx dan') }}" class="border-gray-300 rounded-md w-28">
                <input type="number" name="price_to" value="{{ request('price_to') }}" placeholder="{{ __('Narx gacha') }}" class="border-gray-300 rounded-md w-28">

                <select name="sort" class="border-gray-300 rounded-md">
                    <option value="" @selected(request('sort') === null || request('sort') === '')>{{ __('Saralash') }}</option>
                    <option value="newest" @selected(request('sort') === 'newest')>{{ __('Avval yangilari') }}</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>{{ __('Narx: arzondan qimmatga') }}</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('Narx: qimmatdan arzonga') }}</option>
                </select>

                <x-primary-button>{{ __('Qidirish') }}</x-primary-button>
            </form>

            <x-banner-widget :category-id="request('category_id') ?: null" />

            <div class="text-sm text-gray-500 mb-3 mt-4">
                {{ __(':total ta e\'lon topildi', ['total' => $adverts->total()]) }}
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @forelse ($adverts as $advert)
                    <a href="{{ route('adverts.show', $advert) }}" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        @if ($photo = $advert->photos->first())
                            <img src="{{ $photo->getFileUrl() }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gray-100 dark:bg-gray-700"></div>
                        @endif
                        <div class="p-3">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $advert->title }}</div>
                            <div class="text-sm text-gray-500">{{ $advert->price }}</div>
                        </div>
                    </a>
                @empty
                    <div class="text-gray-500">{{ __("Hech narsa topilmadi") }}</div>
                @endforelse
            </div>

            <div class="mt-4">{{ $adverts->links() }}</div>
        </div>
    </div>
</x-app-layout>
