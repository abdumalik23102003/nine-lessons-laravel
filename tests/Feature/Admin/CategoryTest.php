<?php

use App\Models\Category;
use App\Models\User;

test('guests cannot access categories', function () {
    $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
});

test('a regular user cannot access categories', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.categories.index'))->assertForbidden();
});

test('a moderator can manage categories', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('admin.categories.index'))
        ->assertOk();

    $this->actingAs($moderator)
        ->post(route('admin.categories.store'), [
            'name' => 'Elektronika',
            'slug' => 'elektronika',
            'parent_id' => '',
        ])
        ->assertRedirect(route('admin.categories.index'));

    expect(Category::query()->where('slug', 'elektronika')->exists())->toBeTrue();
});

test('category name must be unique within the same parent', function () {
    $moderator = User::factory()->moderator()->create();
    Category::factory()->create(['name' => 'Elektronika', 'slug' => 'elektronika', 'parent_id' => null]);

    $this->actingAs($moderator)
        ->post(route('admin.categories.store'), [
            'name' => 'Elektronika',
            'slug' => 'elektronika-2',
            'parent_id' => '',
        ])
        ->assertSessionHasErrors('name');
});

test('a category cannot become its own descendants parent', function () {
    $moderator = User::factory()->moderator()->create();
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id]);

    $this->actingAs($moderator)
        ->put(route('admin.categories.update', $parent), [
            'name' => $parent->name,
            'slug' => $parent->slug,
            'parent_id' => $child->id,
        ])
        ->assertSessionHasErrors('parent_id');
});

test('deleting a category that has adverts fails gracefully', function () {
    $moderator = User::factory()->moderator()->create();
    $category = Category::factory()->create();
    \App\Models\Advert::factory()->for($category)->create();

    $this->actingAs($moderator)
        ->delete(route('admin.categories.destroy', $category))
        ->assertRedirect();

    expect(Category::query()->find($category->id))->not->toBeNull();
});
