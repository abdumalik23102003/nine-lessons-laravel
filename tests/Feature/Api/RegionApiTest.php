<?php

use App\Models\Region;

test('regions index returns a tree wrapped in data key', function () {
    Region::factory()->create(['name' => 'Toshkent']);

    $response = $this->getJson(route('api.regions.index'));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                ['id', 'name'],
            ],
        ]);
});
