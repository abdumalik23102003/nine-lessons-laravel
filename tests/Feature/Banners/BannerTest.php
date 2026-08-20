<?php

use App\Models\Banner;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('a user can create a banner with an image', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($user)
        ->post(route('cabinet.banners.store'), [
            'name' => 'Test banner',
            'url' => 'https://example.com',
            'category_id' => $category->id,
            'format' => '728x90',
            'file' => UploadedFile::fake()->image('banner.jpg'),
        ])
        ->assertRedirect();

    $banner = Banner::query()->where('name', 'Test banner')->first();
    expect($banner)->not->toBeNull();
    Storage::disk('public')->assertExists($banner->file);
});

test('a banner without a file cannot be sent to moderation', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->for($user)->create(['file' => null]);

    $this->actingAs($user)->post(route('cabinet.banners.send-to-moderation', $banner));

    expect($banner->fresh()->status)->toBe(Banner::STATUS_DRAFT);
});

test('a banner with a file can be sent to moderation', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->for($user)->create();

    $this->actingAs($user)->post(route('cabinet.banners.send-to-moderation', $banner));

    expect($banner->fresh()->status)->toBe(Banner::STATUS_MODERATION);
});

test('a user cannot edit another users banner', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $banner = Banner::factory()->for($owner)->create();

    $this->actingAs($stranger)
        ->get(route('cabinet.banners.edit', $banner))
        ->assertForbidden();
});

test('clicking a banner link increments its click count and redirects', function () {
    $banner = Banner::factory()->active()->create(['url' => 'https://example.com/landing']);

    $this->get(route('banners.click', $banner))
        ->assertRedirect('https://example.com/landing');

    expect($banner->fresh()->clicks)->toBe(1);
});

test('clicking an inactive banner does not increment its click count', function () {
    $banner = Banner::factory()->create(['status' => Banner::STATUS_DRAFT]);

    $this->get(route('banners.click', $banner));

    expect($banner->fresh()->clicks)->toBe(0);
});
