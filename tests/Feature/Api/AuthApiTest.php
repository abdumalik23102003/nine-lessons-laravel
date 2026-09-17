<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('a user can register and receives a token', function () {
    $response = $this->postJson(route('api.register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.user.email', 'test@example.com')
        ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email'], 'token']]);

    expect(User::query()->where('email', 'test@example.com')->exists())->toBeTrue();
});

test('registration fails with a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->postJson(route('api.register'), [
        'name' => 'Test',
        'email' => 'taken@example.com',
        'password' => 'password123',
    ])->assertStatus(422)->assertJsonValidationErrors('email');
});

test('a user can login with correct credentials', function () {
    $user = User::factory()->create(['password' => Hash::make('password123')]);

    $response = $this->postJson(route('api.login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure(['data' => ['user', 'token']]);
});

test('login fails with an incorrect password', function () {
    $user = User::factory()->create(['password' => Hash::make('password123')]);

    $this->postJson(route('api.login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertStatus(422)->assertJsonValidationErrors('email');
});

test('the me endpoint requires a valid token', function () {
    $this->getJson(route('api.user'))->assertUnauthorized();
});

test('the me endpoint returns the authenticated user', function () {
    $user = User::factory()->create();
    \Laravel\Sanctum\Sanctum::actingAs($user);

    $this->getJson(route('api.user'))
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

test('logout revokes the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('api.logout'))
        ->assertStatus(204);

    // The same token must no longer work.
    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson(route('api.user'))
        ->assertUnauthorized();
});
