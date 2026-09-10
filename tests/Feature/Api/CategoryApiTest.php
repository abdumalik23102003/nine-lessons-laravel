<?php

use App\Models\Category;

test('categories index returns a tree wrapped in data key', function () {
    $parent = Category::factory()->create(['name' => 'Elektronika']);
    Category::factory()->create(['name' => 'Telefonlar', 'parent_id' => $parent->id]);

    $response = $this->getJson(route('api.categories.index'));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                ['id', 'name'],
            ],
        ]);

    $names = collect($response->json('data'))->pluck('name')->all();

    expect($names)->toContain('Elektronika');
});
