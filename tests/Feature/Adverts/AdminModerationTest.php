<?php

use App\Models\Advert;
use App\Models\User;
use App\Services\Search\AdvertIndexer;

beforeEach(function () {
    $this->mock(AdvertIndexer::class)->shouldReceive('index', 'remove')->andReturnNull();
});

test('guests cannot access the moderation page', function () {
    $this->get(route('admin.moderation.index'))->assertRedirect(route('login'));
});

test('a regular user cannot access the moderation page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.moderation.index'))
        ->assertForbidden();
});

test('a moderator can access the moderation page', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('admin.moderation.index'))
        ->assertOk();
});

test('an admin can see adverts on moderation', function () {
    $admin = User::factory()->admin()->create();
    $advert = Advert::factory()->onModeration()->create(['title' => 'Test e\'lon']);

    $this->actingAs($admin)
        ->get(route('admin.moderation.index'))
        ->assertOk()
        ->assertSee('Test e\'lon');
});

test('an admin can approve an advert on moderation', function () {
    $admin = User::factory()->admin()->create();
    $advert = Advert::factory()->onModeration()->create();

    $this->actingAs($admin)
        ->post(route('admin.moderation.approve', $advert), [
            'expires_at' => now()->addMonth()->toDateString(),
        ])
        ->assertRedirect(route('admin.moderation.index'));

    expect($advert->fresh())
        ->status->toBe(Advert::STATUS_ACTIVE)
        ->published_at->not->toBeNull()
        ->expires_at->not->toBeNull();
});

test('an admin can reject an advert on moderation with a reason', function () {
    $admin = User::factory()->admin()->create();
    $advert = Advert::factory()->onModeration()->create();

    $this->actingAs($admin)
        ->post(route('admin.moderation.reject', $advert), [
            'reason' => 'Fotosurat sifatsiz.',
        ])
        ->assertRedirect(route('admin.moderation.index'));

    expect($advert->fresh())
        ->status->toBe(Advert::STATUS_DRAFT)
        ->reject_reason->toBe('Fotosurat sifatsiz.');
});

test('rejecting without a reason fails validation', function () {
    $admin = User::factory()->admin()->create();
    $advert = Advert::factory()->onModeration()->create();

    $this->actingAs($admin)
        ->post(route('admin.moderation.reject', $advert), [])
        ->assertSessionHasErrors('reason');

    expect($advert->fresh()->status)->toBe(Advert::STATUS_MODERATION);
});

test('an already active advert cannot be approved again', function () {
    $admin = User::factory()->admin()->create();
    $advert = Advert::factory()->active()->create();

    $this->actingAs($admin)->post(route('admin.moderation.approve', $advert), [
        'expires_at' => now()->addMonth()->toDateString(),
    ]);

    expect($advert->fresh()->status)->toBe(Advert::STATUS_ACTIVE);
});
