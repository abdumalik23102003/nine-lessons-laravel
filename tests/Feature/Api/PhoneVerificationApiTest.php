<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Notifications\PhoneVerificationCodeNotification;
use Illuminate\Support\Facades\Notification;

test('user can request phone verification', function () {
    Notification::fake();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.phone.request-verification'), [
        'phone' => '+998901234567',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Verification code sent to your phone']);
    Notification::assertSentTo(
        $user,
        PhoneVerificationCodeNotification::class
    );
    $user->refresh();
    expect($user->phone)->toBe('+998901234567');
    expect($user->phone_verification_token)->not->toBeNull();
});

test('phone must be in valid format', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.phone.request-verification'), [
        'phone' => 'invalid-phone',
    ]);

    $response->assertUnprocessable();
});

test('phone must be unique', function () {
    $user1 = User::factory()->create(['phone' => '+998901234567']);
    $user2 = User::factory()->create();

    Sanctum::actingAs($user2);

    $response = $this->postJson(route('api.phone.request-verification'), [
        'phone' => '+998901234567',
    ]);

    $response->assertUnprocessable()
        ->assertJsonFragment(['message' => 'This phone number is already registered']);
});

test('user can verify phone with correct code', function () {
    $user = User::factory()->create(['phone' => '+998901234567']);
    $code = $user->generatePhoneVerificationToken();
    $user->refresh();

    expect($user->phone_verification_token)->toBe($code);

    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.phone.verify'), [
        'code' => $code,
    ]);

    $response->assertOk()
        ->assertJsonFragment(['message' => 'Phone verified successfully']);

    $user->refresh();
    expect($user->isPhoneVerified())->toBeTrue();
    expect($user->phone_verification_token)->toBeNull();
});

test('user cannot verify phone with incorrect code', function () {
    $user = User::factory()->create(['phone' => '+998901234567']);
    $user->generatePhoneVerificationToken();
    $user->refresh();

    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.phone.verify'), [
        'code' => '999999',
    ]);

    $response->assertUnprocessable()
        ->assertJsonFragment(['message' => 'Invalid verification code']);

    $user->refresh();
    expect($user->isPhoneVerified())->toBeFalse();
});

test('verification code must be 6 digits', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.phone.verify'), [
        'code' => '12345',
    ]);

    $response->assertUnprocessable();
});

test('guest cannot verify phone', function () {
    $this->postJson(route('api.phone.verify'), [
        'code' => '123456',
    ])->assertUnauthorized();
});
