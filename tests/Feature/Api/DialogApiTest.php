<?php

use App\Models\Advert;
use App\Models\Dialog;
use App\Models\User;
use App\Services\Search\AdvertIndexer;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->mock(AdvertIndexer::class)->shouldReceive('index', 'remove')->andReturnNull();
});

test('a user can start a dialog with an advert owner', function () {
    $owner = User::factory()->create();
    $client = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();

    Sanctum::actingAs($client);

    $response = $this->postJson(route('api.dialogs.start', $advert));

    $response->assertCreated();
    expect(Dialog::query()->where('advert_id', $advert->id)->where('client_id', $client->id)->exists())->toBeTrue();
});

test('an owner cannot start a dialog with their own advert', function () {
    $owner = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();

    Sanctum::actingAs($owner);

    $this->postJson(route('api.dialogs.start', $advert))->assertForbidden();
});

test('a stranger cannot view a dialog they are not part of', function () {
    $dialog = Dialog::factory()->create();
    $stranger = User::factory()->create();

    Sanctum::actingAs($stranger);

    $this->getJson(route('api.dialogs.show', $dialog))->assertForbidden();
});

test('a client message increments the owners unread counter', function () {
    $dialog = Dialog::factory()->create();
    Sanctum::actingAs(User::find($dialog->client_id));

    $this->postJson(route('api.dialogs.messages.store', $dialog), [
        'message' => 'Salom, hali bormi?',
    ])->assertCreated();

    expect($dialog->fresh())
        ->user_new_messages->toBe(1)
        ->client_new_messages->toBe(0);
});

test('opening a dialog marks it as read for the viewer', function () {
    $dialog = Dialog::factory()->create(['user_new_messages' => 3]);
    Sanctum::actingAs(User::find($dialog->user_id));

    $this->getJson(route('api.dialogs.show', $dialog))
        ->assertOk()
        ->assertJsonPath('data.unread_count', 0);

    expect($dialog->fresh()->user_new_messages)->toBe(0);
});
