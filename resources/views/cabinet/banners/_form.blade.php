@csrf
@if ($banner->exists)
    @method('PUT')
@endif

<div class="mb-4">
    <x-input-label for="name" value="Nomi" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $banner->name) }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="url" value="Havola (bosilganda qayerga o'tadi)" />
    <x-text-input id="url" name="url" type="url" class="mt-1 block w-full" value="{{ old('url', $banner->url) }}" placeholder="https://..." required />
    <x-input-error :messages="$errors->get('url')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="category_id" value="Kategoriya (ixtiyoriy)" />
    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 rounded-md">
        <option value="">{{ __('— Barcha kategoriyalar —') }}</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((int) old('category_id', $banner->category_id) === $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="region_id" value="Hudud (ixtiyoriy)" />
    <select id="region_id" name="region_id" class="mt-1 block w-full border-gray-300 rounded-md">
        <option value="">{{ __('— Barcha hududlar —') }}</option>
        @foreach ($regions as $region)
            <option value="{{ $region->id }}" @selected((int) old('region_id', $banner->region_id) === $region->id)>{{ $region->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('region_id')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="format" value="Format (masalan, 728x90)" />
    <x-text-input id="format" name="format" type="text" class="mt-1 block w-full" value="{{ old('format', $banner->format) }}" />
    <x-input-error :messages="$errors->get('format')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="file" value="Rasm" />
    @if ($banner->file)
        <img src="{{ $banner->getFileUrl() }}" class="h-20 rounded my-2" alt="">
    @endif
    <input id="file" name="file" type="file" accept="image/*" class="mt-1 block w-full">
    <x-input-error :messages="$errors->get('file')" class="mt-2" />
</div>

<div class="flex items-center gap-3">
    <x-primary-button>{{ __('Saqlash') }}</x-primary-button>
    <a href="{{ route('cabinet.banners.index') }}" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Bekor qilish') }}</a>
</div>
