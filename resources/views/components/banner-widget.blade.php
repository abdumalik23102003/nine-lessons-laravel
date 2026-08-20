@props(['categoryId' => null])

@php
    $banner = \App\Models\Banner::query()
        ->active()
        ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
        ->inRandomOrder()
        ->first();

    if ($banner) {
        $banner->recordView();
    }
@endphp

@if ($banner)
    <a href="{{ route('banners.click', $banner) }}" target="_blank" rel="noopener sponsored" class="block">
        <img src="{{ $banner->getFileUrl() }}" alt="{{ $banner->name }}" class="max-w-full rounded-lg">
    </a>
@endif
