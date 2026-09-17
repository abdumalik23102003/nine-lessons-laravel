<?php

use App\Models\Advert;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('advert resource includes attributes and values', function () {
    $category = Category::factory()->create();
    $attribute = Attribute::factory()->for($category)->create(['name' => 'Color']);
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->for($category)->create(['status' => Advert::STATUS_ACTIVE]);
    $advert->values()->create(['attribute_id' => $attribute->id, 'value' => 'Red']);

    Sanctum::actingAs($user);

    $response = $this->getJson(route('api.adverts.show', $advert));

    $response->assertOk()
        ->assertJsonPath('data.attributes.0.attribute_id', $attribute->id)
        ->assertJsonPath('data.attributes.0.attribute_name', 'Color')
        ->assertJsonPath('data.attributes.0.value', 'Red');
});

test('advert with multiple attribute values', function () {
    $category = Category::factory()->create();
    $attr1 = Attribute::factory()->for($category)->create(['name' => 'Brand']);
    $attr2 = Attribute::factory()->for($category)->create(['name' => 'Model']);
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->for($category)->create(['status' => Advert::STATUS_ACTIVE]);
    
    $advert->values()->createMany([
        ['attribute_id' => $attr1->id, 'value' => 'Samsung'],
        ['attribute_id' => $attr2->id, 'value' => 'Galaxy S24'],
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson(route('api.adverts.show', $advert));

    $response->assertOk()
        ->assertJsonCount(2, 'data.attributes');
});
