<?php

use App\Models\User;

test('new user starts in wait status', function () {
    $user = User::factory()->create();

    expect($user->status)->toBe(User::STATUS_WAIT);
    expect($user->isWaiting())->toBeTrue();
});

test('waiting user can be activated', function () {
    $user = User::factory()->create(['status' => User::STATUS_WAIT]);

    $user->activate();
    $user->refresh();

    expect($user->isActive())->toBeTrue();
    expect($user->activated_at)->not->toBeNull();
});

test('cannot activate non-waiting user', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);

    $this->expectException(\DomainException::class);
    $user->activate();
});

test('active user can be suspended', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);

    $user->suspend();
    $user->refresh();

    expect($user->isSuspended())->toBeTrue();
});

test('suspended user can be unsuspended', function () {
    $user = User::factory()->create(['status' => User::STATUS_SUSPENDED]);

    $user->unsuspend();
    $user->refresh();

    expect($user->isActive())->toBeTrue();
});

test('cannot unsuspend non-suspended user', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);

    $this->expectException(\DomainException::class);
    $user->unsuspend();
});

test('cannot suspend deleted user', function () {
    $user = User::factory()->create(['status' => User::STATUS_DELETED]);

    $this->expectException(\DomainException::class);
    $user->suspend();
});

test('user statuses list is available', function () {
    $statuses = User::statusesList();

    expect($statuses)->toHaveKey(User::STATUS_WAIT);
    expect($statuses)->toHaveKey(User::STATUS_ACTIVE);
    expect($statuses)->toHaveKey(User::STATUS_SUSPENDED);
    expect($statuses)->toHaveKey(User::STATUS_DELETED);
});
