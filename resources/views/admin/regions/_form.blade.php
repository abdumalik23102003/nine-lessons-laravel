@csrf
@if ($region->exists)
    @method('PUT')
@endif

<div class="mb-4">
    <x-input-label for="name" value="Nomi" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $region->name) }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="slug" value="Slug (URL uchun)" />
    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug', $region->slug) }}" required />
    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="parent_id" value="Ota hudud" />
    <select id="parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 rounded-md">
        <option value="">{{ __('— Tepa daraja —') }}</option>
        @foreach ($tree as $node)
            <option value="{{ $node['id'] }}" @selected((int) old('parent_id', $region->parent_id) === $node['id'])>{{ $node['name'] }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
</div>

<div class="flex items-center gap-3">
    <x-primary-button>{{ __('Saqlash') }}</x-primary-button>
    <a href="{{ route('admin.regions.index') }}" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Bekor qilish') }}</a>
</div>
