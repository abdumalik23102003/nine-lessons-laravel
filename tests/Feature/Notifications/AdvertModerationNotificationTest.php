<?php

use App\Models\Advert;
use App\Models\User;
use App\Notifications\AdvertModerationApprovedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

test('notification sent when advert approved from moderation', function () {
    Notification::fake();

    $user = User::factory()->create();
    $cat = \App\Models\Category::factory()->create();
    $advert = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_MODERATION,
    ]);

    $advert->moderate(now()->addDays(30));

    Notification::assertSentTo(
        [$user],
        AdvertModerationApprovedNotification::class
    );
});

test('notification contains advert details', function () {
    Notification::fake();

    $user = User::factory()->create();
    $cat = \App\Models\Category::factory()->create();
    $advert = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_MODERATION,
        'title' => 'iPhone 15 Pro',
    ]);

    $advert->moderate(now()->addDays(30));

    Notification::assertSentTo(
        [$user],
        AdvertModerationApprovedNotification::class,
        function ($notification) use ($advert) {
            return $notification->advert->id === $advert->id;
        }
    );
});

test('notification not sent if advert not in moderation', function () {
    Notification::fake();

    $user = User::factory()->create();
    $cat = \App\Models\Category::factory()->create();
    $advert = Advert::factory()->for($user)->for($cat)->create([
        'status' => Advert::STATUS_DRAFT,
    ]);

    try {
        $advert->moderate(now()->addDays(30));
    } catch (\DomainException $e) {
        // Expected
    }

    Notification::assertNotSentTo([$user], AdvertModerationApprovedNotification::class);
});
