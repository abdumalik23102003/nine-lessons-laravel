@csrf
<div class="mb-4">
    <x-input-label for="category_id" value="Kategoriya" />
    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 rounded-md">
        <option value="">— tanlang —</option>
        @foreach ($categories as $category)
            <option value="{{ $category['id'] }}" @selected(old('category_id', $advert->category_id ?? null) == $category['id'])>
                {{ $category['name'] }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="region_id" value="Hudud" />
    <select id="region_id" name="region_id" class="mt-1 block w-full border-gray-300 rounded-md">
        <option value="">— tanlang —</option>
        @foreach ($regions as $region)
            <option value="{{ $region['id'] }}" @selected(old('region_id', $advert->region_id ?? null) == $region['id'])>
                {{ $region['name'] }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('region_id')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="title" value="Sarlavha" />
    <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title', $advert->title ?? '') }}" />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="price" value="Narx" />
    <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" value="{{ old('price', $advert->price ?? '') }}" />
    <x-input-error :messages="$errors->get('price')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="address" value="Manzil" />
    <x-text-input id="address" name="address" class="mt-1 block w-full" value="{{ old('address', $advert->address ?? '') }}" />
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="content" value="Tavsif" />
    <textarea id="content" name="content" rows="6" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('content', $advert->content ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('content')" class="mt-2" />
</div>

<x-primary-button>Saqlash</x-primary-button>
