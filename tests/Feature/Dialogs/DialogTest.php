<?php

use App\Models\Advert;
use App\Models\Dialog;
use App\Models\User;
use App\Services\Search\AdvertIndexer;

beforeEach(function () {
    $this->mock(AdvertIndexer::class)->shouldReceive('index', 'remove')->andReturnNull();
});

test('a user can start a dialog with an advert owner', function () {
    $owner = User::factory()->create();
    $client = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();

    $this->actingAs($client)
        ->post(route('cabinet.dialogs.start', $advert))
        ->assertRedirect();

    expect(Dialog::query()->where('advert_id', $advert->id)->where('client_id', $client->id)->exists())->toBeTrue();
});

test('an owner cannot start a dialog with their own advert', function () {
    $owner = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();

    $this->actingAs($owner)
        ->post(route('cabinet.dialogs.start', $advert))
        ->assertForbidden();
});

test('starting a dialog twice reuses the same dialog', function () {
    $owner = User::factory()->create();
    $client = User::factory()->create();
    $advert = Advert::factory()->for($owner)->create();

    $this->actingAs($client)->post(route('cabinet.dialogs.start', $advert));
    $this->actingAs($client)->post(route('cabinet.dialogs.start', $advert));

    expect(Dialog::query()->where('advert_id', $advert->id)->where('client_id', $client->id)->count())->toBe(1);
});

test('a stranger cannot view a dialog they are not part of', function () {
    $dialog = Dialog::factory()->create();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->get(route('cabinet.dialogs.show', $dialog))
        ->assertForbidden();
});

test('a client message increments the owners unread counter', function () {
    $dialog = Dialog::factory()->create();

    $this->actingAs(User::find($dialog->client_id))
        ->post(route('cabinet.dialogs.messages.store', $dialog), ['message' => 'Salom, hali bormi?']);

    expect($dialog->fresh())
        ->user_new_messages->toBe(1)
        ->client_new_messages->toBe(0);
});

test('an owner message increments the clients unread counter', function () {
    $dialog = Dialog::factory()->create();

    $this->actingAs(User::find($dialog->user_id))
        ->post(route('cabinet.dialogs.messages.store', $dialog), ['message' => 'Ha, bor.']);

    expect($dialog->fresh())
        ->client_new_messages->toBe(1)
        ->user_new_messages->toBe(0);
});

test('opening a dialog marks it as read for the viewer', function () {
    $dialog = Dialog::factory()->create(['user_new_messages' => 3]);

    $this->actingAs(User::find($dialog->user_id))
        ->get(route('cabinet.dialogs.show', $dialog));

    expect($dialog->fresh()->user_new_messages)->toBe(0);
});
