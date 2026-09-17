<?php

use App\Models\Advert;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('a user can send their draft advert to moderation', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create(['status' => Advert::STATUS_DRAFT]);
    $advert->photos()->create(['file' => 'test.jpg']);
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.adverts.send-to-moderation', $advert));

    $response->assertOk()
        ->assertJsonPath('data.status', Advert::STATUS_MODERATION);

    expect($advert->fresh()->status)->toBe(Advert::STATUS_MODERATION);
});

test('cannot send advert to moderation without photos', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create(['status' => Advert::STATUS_DRAFT]);
    Sanctum::actingAs($user);

    $this->postJson(route('api.adverts.send-to-moderation', $advert))
        ->assertStatus(422)
        ->assertJsonPath('message', 'Upload photos.');
});

test('cannot send non-draft advert to moderation', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create(['status' => Advert::STATUS_ACTIVE]);
    Sanctum::actingAs($user);

    $this->postJson(route('api.adverts.send-to-moderation', $advert))
        ->assertStatus(422)
        ->assertJsonPath('message', 'Advert is not draft.');
});

test('a stranger cannot send someone elses advert to moderation', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create(['status' => Advert::STATUS_DRAFT]);
    $advert->photos()->create(['file' => 'test.jpg']);
    Sanctum::actingAs($stranger);

    $this->postJson(route('api.adverts.send-to-moderation', $advert))
        ->assertForbidden();
});

test('a user can close their active advert', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create([
        'status' => Advert::STATUS_ACTIVE,
        'published_at' => now(),
    ]);
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.adverts.close', $advert));

    $response->assertOk()
        ->assertJsonPath('data.status', Advert::STATUS_CLOSED);

    expect($advert->fresh()->status)->toBe(Advert::STATUS_CLOSED);
});

test('cannot close non-active advert', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create(['status' => Advert::STATUS_DRAFT]);
    Sanctum::actingAs($user);

    $this->postJson(route('api.adverts.close', $advert))
        ->assertStatus(422)
        ->assertJsonPath('message', 'Advert is not active.');
});

test('a stranger cannot close someone elses advert', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create(['status' => Advert::STATUS_ACTIVE]);
    Sanctum::actingAs($stranger);

    $this->postJson(route('api.adverts.close', $advert))
        ->assertForbidden();
});
