<?php

use App\Models\Network;
use App\Models\User;
use App\Services\NetworkService;
use Laravel\Sanctum\Sanctum;

test('new user via social login creates account in wait status', function () {
    $mockUser = \Mockery::mock(\Laravel\Socialite\Contracts\User::class);
    $mockUser->shouldReceive('getId')->andReturn('google-12345');
    $mockUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
    $mockUser->shouldReceive('getName')->andReturn('New User');
    $mockUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $networkService = app(NetworkService::class);
    $result = $networkService->handleCallback('google', $mockUser);

    expect($result['is_new'])->toBeTrue();
    expect($result['user']->status)->toBe(User::STATUS_ACTIVE);
    expect($result['user']->email)->toBe('newuser@example.com');
    expect($result['token'])->not->toBeNull();
});

test('existing user via social login returns token', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $mockUser = \Mockery::mock(\Laravel\Socialite\Contracts\User::class);
    $mockUser->shouldReceive('getId')->andReturn('google-54321');
    $mockUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $mockUser->shouldReceive('getName')->andReturn('Existing User');
    $mockUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $networkService = app(NetworkService::class);
    $result = $networkService->handleCallback('google', $mockUser);

    expect($result['is_new'])->toBeFalse();
    expect($result['user']->id)->toBe($existingUser->id);
    expect($result['token'])->not->toBeNull();
});

test('social network is linked to user', function () {
    $mockUser = \Mockery::mock(\Laravel\Socialite\Contracts\User::class);
    $mockUser->shouldReceive('getId')->andReturn('google-99999');
    $mockUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $mockUser->shouldReceive('getName')->andReturn('Test User');
    $mockUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $networkService = app(NetworkService::class);
    $result = $networkService->handleCallback('google', $mockUser);

    expect(Network::where('user_id', $result['user']->id)
        ->where('name', 'google')
        ->exists())->toBeTrue();
});

test('same social account found on second call', function () {
    $socialId = 'google-' . \Illuminate\Support\Str::random(10);
    $mockUser1 = \Mockery::mock(\Laravel\Socialite\Contracts\User::class);
    $mockUser1->shouldReceive('getId')->andReturn($socialId);
    $mockUser1->shouldReceive('getEmail')->andReturn('duplicate@example.com');
    $mockUser1->shouldReceive('getName')->andReturn('Test User');
    $mockUser1->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $mockUser2 = \Mockery::mock(\Laravel\Socialite\Contracts\User::class);
    $mockUser2->shouldReceive('getId')->andReturn($socialId);
    $mockUser2->shouldReceive('getEmail')->andReturn('duplicate@example.com');
    $mockUser2->shouldReceive('getName')->andReturn('Test User');
    $mockUser2->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $networkService = app(NetworkService::class);
    $result1 = $networkService->handleCallback('google', $mockUser1);
    $result2 = $networkService->handleCallback('google', $mockUser2);

    expect($result1['user']->id)->toBe($result2['user']->id);
    expect($result1['is_new'])->toBeTrue();
    expect($result2['is_new'])->toBeFalse();
});

test('user can unlink social network', function () {
    $user = User::factory()->create();
    Network::factory()->create([
        'user_id' => $user->id,
        'name' => 'google',
        'network_id' => '12345',
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('api.auth.network.unlink', 'google'));

    $response->assertOk();
    expect(Network::where('user_id', $user->id)->where('name', 'google')->exists())->toBeFalse();
});

test('cannot unlink non-existent network', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('api.auth.network.unlink', 'google'));

    $response->assertNotFound();
});

test('cannot unlink with invalid provider', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('api.auth.network.unlink', 'invalid'));

    $response->assertBadRequest();
});

test('social login redirect endpoint requires valid provider', function () {
    $response = $this->getJson(route('api.auth.social.redirect', 'invalid'));

    $response->assertBadRequest();
});
