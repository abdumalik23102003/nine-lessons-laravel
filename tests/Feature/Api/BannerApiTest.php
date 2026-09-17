<?php

use App\Models\Banner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Storage::fake('public');
});

test('guests cannot list their banners', function () {
    $this->getJson(route('api.cabinet.banners.index'))->assertUnauthorized();
});

test('a user can create a banner with an image via api', function () {
    $user = User::factory()->create();
    $category = \App\Models\Category::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->post(route('api.banners.store'), [
        'name' => 'Test banner',
        'url' => 'https://example.com',
        'category_id' => $category->id,
        'file' => UploadedFile::fake()->image('banner.jpg'),
    ], ['Accept' => 'application/json']);

    $response->assertCreated()->assertJsonPath('data.name', 'Test banner');

    $banner = Banner::query()->where('name', 'Test banner')->first();
    Storage::disk('public')->assertExists($banner->file);
});

test('a stranger cannot update someone elses banner', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $banner = Banner::factory()->for($owner)->create();

    Sanctum::actingAs($stranger);

    $this->postJson(route('api.banners.update', $banner), [
        'name' => 'Hack',
        'url' => 'https://evil.example.com',
    ])->assertForbidden();
});

test('a banner without a file cannot be sent to moderation', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->for($user)->create(['file' => null]);
    Sanctum::actingAs($user);

    $this->postJson(route('api.banners.send-to-moderation', $banner))
        ->assertStatus(422);
});

test('a banner with a file can be sent to moderation', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->for($user)->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.banners.send-to-moderation', $banner))
        ->assertOk()
        ->assertJsonPath('data.status', Banner::STATUS_MODERATION);
});

test('an owner can delete their own banner', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->for($user)->create();
    Sanctum::actingAs($user);

    $this->deleteJson(route('api.banners.destroy', $banner))->assertStatus(204);

    expect(Banner::query()->find($banner->id))->toBeNull();
});
