<?php

use App\Models\Banner;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('banner can be created', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.banners.store'), [
        'name' => 'Test Banner',
        'url' => 'https://example.com',
        'format' => '300x250',
    ]);

    $response->assertCreated();
    expect(Banner::where('name', 'Test Banner')->exists())->toBeTrue();
});

test('banner can be updated by owner', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.banners.update', $banner), [
        'name' => 'Updated Banner',
        'url' => 'https://updated.com',
    ]);

    $response->assertOk();
    $banner->refresh();
    expect($banner->name)->toBe('Updated Banner');
});

test('stranger cannot update banner', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $banner = Banner::factory()->create(['user_id' => $user1->id]);

    Sanctum::actingAs($user2);

    $this->postJson(route('api.banners.update', $banner), [
        'name' => 'Hacked',
        'url' => 'https://hacked.com',
        'format' => '300x250',
    ])->assertForbidden();
});

test('banner can be deleted by owner', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $this->deleteJson(route('api.banners.destroy', $banner))
        ->assertNoContent();

    expect(Banner::find($banner->id))->toBeNull();
});

test('banner can be sent to moderation', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->create([
        'user_id' => $user->id,
        'status' => Banner::STATUS_DRAFT,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson(
        route('api.banners.send-to-moderation', $banner)
    );

    $response->assertOk();
    $banner->refresh();
    expect($banner->status)->toBe(Banner::STATUS_MODERATION);
});

test('cannot send non-draft banner to moderation', function () {
    $user = User::factory()->create();
    $banner = Banner::factory()->create([
        'user_id' => $user->id,
        'status' => Banner::STATUS_ACTIVE,
    ]);

    Sanctum::actingAs($user);

    $this->postJson(route('api.banners.send-to-moderation', $banner))
        ->assertUnprocessable();
});
