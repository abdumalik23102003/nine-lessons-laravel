<?php

use App\Models\Banner;
use App\Models\User;

test('a regular user cannot access banner moderation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.banners.moderation.index'))
        ->assertForbidden();
});

test('a moderator can approve a banner on moderation', function () {
    $moderator = User::factory()->moderator()->create();
    $banner = Banner::factory()->onModeration()->create();

    $this->actingAs($moderator)
        ->post(route('admin.banners.moderation.approve', $banner), [
            'expires_at' => now()->addMonth()->toDateString(),
        ])
        ->assertRedirect(route('admin.banners.moderation.index'));

    expect($banner->fresh())
        ->status->toBe(Banner::STATUS_ACTIVE)
        ->published_at->not->toBeNull();
});

test('a moderator can reject a banner with a reason', function () {
    $moderator = User::factory()->moderator()->create();
    $banner = Banner::factory()->onModeration()->create();

    $this->actingAs($moderator)
        ->post(route('admin.banners.moderation.reject', $banner), [
            'reason' => 'Rasm sifati past.',
        ])
        ->assertRedirect(route('admin.banners.moderation.index'));

    expect($banner->fresh())
        ->status->toBe(Banner::STATUS_DRAFT)
        ->reject_reason->toBe('Rasm sifati past.');
});
