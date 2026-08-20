<?php

use App\Models\User;

test('a regular user cannot access user management', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
});

test('a moderator can view users but cannot change roles', function () {
    $moderator = User::factory()->moderator()->create();
    $target = User::factory()->create(['name' => 'Old Name']);

    $this->actingAs($moderator)
        ->get(route('admin.users.index'))
        ->assertOk();

    $this->actingAs($moderator)
        ->put(route('admin.users.update', $target), [
            'name' => 'New Name',
            'email' => $target->email,
            'role' => User::ROLE_ADMIN,
        ])
        ->assertRedirect(route('admin.users.index'));

    expect($target->fresh())
        ->name->toBe('New Name')
        ->role->toBe(User::ROLE_USER);
});

test('an admin can change another users role', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create();

    $this->actingAs($admin)->put(route('admin.users.update', $target), [
        'name' => $target->name,
        'email' => $target->email,
        'role' => User::ROLE_MODERATOR,
    ]);

    expect($target->fresh()->role)->toBe(User::ROLE_MODERATOR);
});

test('an admin cannot change their own role via the form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->put(route('admin.users.update', $admin), [
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => User::ROLE_USER,
    ]);

    expect($admin->fresh()->role)->toBe(User::ROLE_ADMIN);
});

test('a user cannot delete their own account from admin panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertForbidden();

    expect(User::query()->find($admin->id))->not->toBeNull();
});

test('a moderator cannot delete users', function () {
    $moderator = User::factory()->moderator()->create();
    $target = User::factory()->create();

    $this->actingAs($moderator)
        ->delete(route('admin.users.destroy', $target))
        ->assertForbidden();
});
