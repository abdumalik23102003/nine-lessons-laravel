<?php

use App\Models\Advert;
use App\Models\Photo;
use App\Models\User;
use App\Services\Search\AdvertIndexer;

beforeEach(function () {
    $this->mock(AdvertIndexer::class)->shouldReceive('index', 'remove')->andReturnNull();
});

test('owner can send a draft advert with a photo to moderation', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();
    Photo::factory()->for($advert)->create();

    $this->actingAs($user)
        ->post(route('cabinet.adverts.send-to-moderation', $advert))
        ->assertRedirect(route('cabinet.adverts.edit', $advert));

    expect($advert->fresh()->status)->toBe(Advert::STATUS_MODERATION);
});

test('advert without photos cannot be sent to moderation', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('cabinet.adverts.send-to-moderation', $advert));

    expect($advert->fresh()->status)->toBe(Advert::STATUS_DRAFT);
});

test('a user cannot send another users advert to moderation', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();
    Photo::factory()->for($advert)->create();

    $this->actingAs($stranger)
        ->post(route('cabinet.adverts.send-to-moderation', $advert))
        ->assertForbidden();
});

test('sending a rejected advert back to moderation clears the reject reason', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create(['reject_reason' => 'Rasm sifatsiz.']);
    Photo::factory()->for($advert)->create();

    $this->actingAs($user)
        ->post(route('cabinet.adverts.send-to-moderation', $advert));

    expect($advert->fresh())
        ->status->toBe(Advert::STATUS_MODERATION)
        ->reject_reason->toBeNull();
});
