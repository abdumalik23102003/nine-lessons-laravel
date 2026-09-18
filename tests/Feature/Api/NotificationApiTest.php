<?php

use App\Models\Advert;
use App\Models\User;
use App\Notifications\AdvertRejectedNotification;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

test('user can list their notifications', function () {
    Notification::fake();
    
    $user = User::factory()->create();
    $user->notify(new AdvertRejectedNotification(
        Advert::factory()->create(),
        'Low quality images'
    ));

    // Create notification manually for testing
    \Illuminate\Notifications\DatabaseNotification::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'type' => AdvertRejectedNotification::class,
        'data' => json_encode(['reason' => 'Low quality images']),
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson(route('api.notifications.index'));

    $response->assertOk()
        ->assertJsonStructure(['data', 'meta']);
});

test('user can mark notification as read', function () {
    $user = User::factory()->create();

    $notification = \Illuminate\Notifications\DatabaseNotification::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'type' => AdvertRejectedNotification::class,
        'data' => json_encode(['reason' => 'Bad description']),
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson(
        route('api.notifications.read', $notification->id)
    );

    $response->assertOk();
    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});

test('user can mark all notifications as read', function () {
    $user = User::factory()->create();

    \Illuminate\Notifications\DatabaseNotification::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'type' => AdvertRejectedNotification::class,
        'data' => json_encode(['reason' => 'Reason 1']),
    ]);

    \Illuminate\Notifications\DatabaseNotification::create([
        'id' => \Illuminate\Support\Str::uuid(),
        'notifiable_id' => $user->id,
        'notifiable_type' => User::class,
        'type' => AdvertRejectedNotification::class,
        'data' => json_encode(['reason' => 'Reason 2']),
    ]);

    Sanctum::actingAs($user);

    $this->postJson(route('api.notifications.read-all'))
        ->assertOk();

    expect($user->unreadNotifications()->count())->toBe(0);
});

test('guest cannot list notifications', function () {
    $this->getJson(route('api.notifications.index'))
        ->assertUnauthorized();
});
