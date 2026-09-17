<?php

use App\Models\Advert;
use App\Models\User;
use Carbon\Carbon;

test('expire adverts command expires active adverts past expiry date', function () {
    $user = User::factory()->create();
    $cat = \App\Models\Category::factory()->create();
    
    $expired = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_ACTIVE,
        'expires_at' => now()->subDay(),
    ]);
    
    $active = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_ACTIVE,
        'expires_at' => now()->addDay(),
    ]);
    
    $this->artisan('expire:adverts')
        ->assertSuccessful()
        ->expectsOutput('Expired 1 advert(s).');
    
    expect($expired->fresh()->status)->toBe(Advert::STATUS_CLOSED);
    expect($active->fresh()->status)->toBe(Advert::STATUS_ACTIVE);
});

test('expire adverts does not expire draft adverts', function () {
    $user = User::factory()->create();
    $cat = \App\Models\Category::factory()->create();
    
    $draft = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_DRAFT,
        'expires_at' => now()->subDay(),
    ]);
    
    $this->artisan('expire:adverts')->assertSuccessful();
    
    expect($draft->fresh()->status)->toBe(Advert::STATUS_DRAFT);
});

test('expire adverts does not expire already closed adverts', function () {
    $user = User::factory()->create();
    $cat = \App\Models\Category::factory()->create();
    
    $closed = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_CLOSED,
        'expires_at' => now()->subDay(),
    ]);
    
    $this->artisan('expire:adverts')->assertSuccessful();
    
    expect($closed->fresh()->status)->toBe(Advert::STATUS_CLOSED);
});

test('advert expired correctly when past expiry date', function () {
    $user = User::factory()->create();
    $cat = \App\Models\Category::factory()->create();
    
    $advert = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_ACTIVE,
        'published_at' => now()->subDays(30),
        'expires_at' => now()->subHours(2),
    ]);
    
    expect($advert->isActive())->toBeTrue();
    
    $this->artisan('expire:adverts')->assertSuccessful();
    
    $advert->refresh();
    expect($advert->status)->toBe(Advert::STATUS_CLOSED);
});
