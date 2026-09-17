<?php

use App\Models\Advert;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Storage::fake('public');
});

test('guests cannot upload photos', function () {
    $advert = Advert::factory()->create();

    $this->postJson(route('api.adverts.photos.store', $advert), [
        'photos' => [UploadedFile::fake()->image('photo.jpg')],
    ])->assertUnauthorized();
});

test('a user can upload photos to their advert', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.adverts.photos.store', $advert), [
        'photos' => [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.png'),
        ],
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => [['id', 'url'], ['id', 'url']]])
        ->assertJsonCount(2, 'data');

    expect($advert->photos()->count())->toBe(2);
});

test('a stranger cannot upload photos to someone elses advert', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();
    Sanctum::actingAs($stranger);

    $this->postJson(route('api.adverts.photos.store', $advert), [
        'photos' => [UploadedFile::fake()->image('photo.jpg')],
    ])->assertForbidden();
});

test('cannot upload more than 10 photos at once', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();
    Sanctum::actingAs($user);

    $photos = array_fill(0, 11, UploadedFile::fake()->image('photo.jpg'));

    $this->postJson(route('api.adverts.photos.store', $advert), [
        'photos' => $photos,
    ])->assertStatus(422)
        ->assertJsonValidationErrors('photos');
});

test('can only upload image files', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();
    Sanctum::actingAs($user);

    $this->postJson(route('api.adverts.photos.store', $advert), [
        'photos' => [UploadedFile::fake()->create('file.pdf')],
    ])->assertStatus(422)
        ->assertJsonValidationErrors('photos.0');
});

test('a user can delete their own photo', function () {
    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();
    $photo = $advert->photos()->create(['file' => 'adverts/test.jpg']);
    Sanctum::actingAs($user);

    $this->deleteJson(route('api.adverts.photos.destroy', [$advert, $photo]))
        ->assertStatus(204);

    expect($advert->photos()->count())->toBe(0);
});

test('a stranger cannot delete someone elses photo', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();
    $photo = $advert->photos()->create(['file' => 'adverts/test.jpg']);
    Sanctum::actingAs($stranger);

    $this->deleteJson(route('api.adverts.photos.destroy', [$advert, $photo]))
        ->assertForbidden();
});

test('deleting a photo removes the file', function () {
    Storage::shouldReceive('disk')
        ->with('public')
        ->andReturnSelf()
        ->shouldReceive('delete')
        ->once()
        ->with('adverts/test.jpg');

    $user = User::factory()->create();
    $advert = Advert::factory()->for($user)->create();
    $photo = $advert->photos()->create(['file' => 'adverts/test.jpg']);
    Sanctum::actingAs($user);

    $this->deleteJson(route('api.adverts.photos.destroy', [$advert, $photo]))
        ->assertStatus(204);
});
