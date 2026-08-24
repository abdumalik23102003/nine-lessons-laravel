@csrf
@if ($page->exists)
    @method('PUT')
@endif

<div class="mb-4">
    <x-input-label for="title" value="Sarlavha" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $page->title) }}" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="menu_title" value="Menyu nomi (ixtiyoriy, bo'sh bo'lsa sarlavha ishlatiladi)" />
    <x-text-input id="menu_title" name="menu_title" type="text" class="mt-1 block w-full" value="{{ old('menu_title', $page->menu_title) }}" />
    <x-input-error :messages="$errors->get('menu_title')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="slug" value="Slug (URL uchun, masalan: biz-haqimizda)" />
    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" value="{{ old('slug', $page->slug) }}" required />
    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="content" value="Matn" />
    <textarea id="content" name="content" rows="10" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('content', $page->content) }}</textarea>
    <x-input-error :messages="$errors->get('content')" class="mt-2" />
</div>

<div class="mb-4 flex items-center gap-2">
    <input id="show_in_menu" name="show_in_menu" type="checkbox" value="1" class="rounded border-gray-300" @checked(old('show_in_menu', $page->show_in_menu))>
    <x-input-label for="show_in_menu" value="Footer menyusida ko'rsatilsin" />
</div>

<div class="flex items-center gap-3">
    <x-primary-button>{{ __('Saqlash') }}</x-primary-button>
    <a href="{{ route('admin.pages.index') }}" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Bekor qilish') }}</a>
</div>
