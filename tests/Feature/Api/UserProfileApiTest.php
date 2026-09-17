<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests cannot update profile', function () {
    $this->putJson(route('api.user.update'), [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ])->assertUnauthorized();
});

test('a user can update their profile', function () {
    $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);
    Sanctum::actingAs($user);

    $response = $this->putJson(route('api.user.update'), [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'New Name')
        ->assertJsonPath('data.email', 'new@example.com');

    expect($user->fresh()->name)->toBe('New Name');
    expect($user->fresh()->email)->toBe('new@example.com');
});

test('name is required', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->putJson(route('api.user.update'), [
        'email' => 'test@example.com',
    ])->assertStatus(422)
        ->assertJsonValidationErrors('name');
});

test('email is required', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->putJson(route('api.user.update'), [
        'name' => 'Test User',
    ])->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('email must be valid', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->putJson(route('api.user.update'), [
        'name' => 'Test User',
        'email' => 'invalid-email',
    ])->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('email must be unique except for current user', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);
    Sanctum::actingAs($user1);

    $this->putJson(route('api.user.update'), [
        'name' => 'User One',
        'email' => 'user2@example.com',
    ])->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('user can keep their current email', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    Sanctum::actingAs($user);

    $response = $this->putJson(route('api.user.update'), [
        'name' => 'Updated Name',
        'email' => 'test@example.com',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.email', 'test@example.com');
});
