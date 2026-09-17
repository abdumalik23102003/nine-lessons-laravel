<?php

use App\Models\Banner;
use App\Models\User;

test('expire banners command expires active banners past expiry date', function () {
    $user = User::factory()->create();
    
    $expired = Banner::factory()->for($user)->create([
        'status' => Banner::STATUS_ACTIVE,
        'expires_at' => now()->subDay(),
    ]);
    
    $active = Banner::factory()->for($user)->create([
        'status' => Banner::STATUS_ACTIVE,
        'expires_at' => now()->addDay(),
    ]);
    
    $this->artisan('expire:banners')
        ->assertSuccessful()
        ->expectsOutput('Expired 1 banner(s).');
    
    expect($expired->fresh()->status)->toBe(Banner::STATUS_CLOSED);
    expect($active->fresh()->status)->toBe(Banner::STATUS_ACTIVE);
});

test('expire banners does not expire draft banners', function () {
    $user = User::factory()->create();
    
    $draft = Banner::factory()->for($user)->create([
        'status' => Banner::STATUS_DRAFT,
        'expires_at' => now()->subDay(),
    ]);
    
    $this->artisan('expire:banners')->assertSuccessful();
    
    expect($draft->fresh()->status)->toBe(Banner::STATUS_DRAFT);
});

test('expire banners does not expire already closed banners', function () {
    $user = User::factory()->create();
    
    $closed = Banner::factory()->for($user)->create([
        'status' => Banner::STATUS_CLOSED,
        'expires_at' => now()->subDay(),
    ]);
    
    $this->artisan('expire:banners')->assertSuccessful();
    
    expect($closed->fresh()->status)->toBe(Banner::STATUS_CLOSED);
});

test('banner expired correctly when past expiry date', function () {
    $user = User::factory()->create();
    
    $banner = Banner::factory()->for($user)->create([
        'status' => Banner::STATUS_ACTIVE,
        'published_at' => now()->subDays(30),
        'expires_at' => now()->subHours(2),
    ]);
    
    expect($banner->isActive())->toBeTrue();
    
    $this->artisan('expire:banners')->assertSuccessful();
    
    $banner->refresh();
    expect($banner->status)->toBe(Banner::STATUS_CLOSED);
});
