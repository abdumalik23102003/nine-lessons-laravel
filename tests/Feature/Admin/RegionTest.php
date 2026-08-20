<?php

use App\Models\Region;
use App\Models\User;

test('guests cannot access regions', function () {
    $this->get(route('admin.regions.index'))->assertRedirect(route('login'));
});

test('a moderator cannot manage regions, only admin can', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('admin.regions.index'))
        ->assertForbidden();
});

test('an admin can manage regions', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.regions.store'), [
            'name' => 'Toshkent',
            'slug' => 'tashkent',
            'parent_id' => '',
        ])
        ->assertRedirect(route('admin.regions.index'));

    expect(Region::query()->where('slug', 'tashkent')->exists())->toBeTrue();
});

test('region name must be unique within the same parent', function () {
    $admin = User::factory()->admin()->create();
    Region::factory()->create(['name' => 'Toshkent', 'slug' => 'tashkent', 'parent_id' => null]);

    $this->actingAs($admin)
        ->post(route('admin.regions.store'), [
            'name' => 'Toshkent',
            'slug' => 'tashkent-2',
            'parent_id' => '',
        ])
        ->assertSessionHasErrors('name');
});
