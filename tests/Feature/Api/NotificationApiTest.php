<?php

use App\Models\Advert;
use App\Models\User;
use App\Notifications\AdvertRejectedNotification;
use Laravel\Sanctum\Sanctum;

test('user can list their notifications', function () {
    $user = User::factory()->create();
    $user->notify(new AdvertRejectedNotification(
        Advert::factory()->create(),
        'Low quality images'
    ));

    Sanctum::actingAs($user);

    $response = $this->getJson(route('api.notifications.index'));

    $response->assertOk()
        ->assertJsonStructure(['data', 'meta']);
});

test('user can mark notification as read', function () {
    $user = User::factory()->create();
    $user->notify(new AdvertRejectedNotification(
        Advert::factory()->create(),
        'Bad description'
    ));

    $notification = $user->notifications()->first();

    Sanctum::actingAs($user);

    $response = $this->postJson(
        route('api.notifications.read', $notification->id)
    );

    $response->assertOk();
    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('user can mark all notifications as read', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->create();

    $user->notify(new AdvertRejectedNotification($advert, 'Reason 1'));
    $user->notify(new AdvertRejectedNotification($advert, 'Reason 2'));

    Sanctum::actingAs($user);

    $this->postJson(route('api.notifications.read-all'))
        ->assertOk();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('guest cannot list notifications', function () {
    $this->getJson(route('api.notifications.index'))
        ->assertUnauthorized();
});
