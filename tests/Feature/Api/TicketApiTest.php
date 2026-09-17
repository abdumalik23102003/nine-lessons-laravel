<?php

use App\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('guests cannot access tickets', function () {
    $this->getJson(route('api.tickets.index'))->assertUnauthorized();
});

test('a user can create a ticket via api', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.tickets.store'), [
        'subject' => 'Yordam kerak',
        'content' => 'Muammom bor.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.subject', 'Yordam kerak')
        ->assertJsonPath('data.status', Ticket::STATUS_OPEN);
});

test('a user only sees their own tickets in the index', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    Ticket::factory()->for($user)->create(['subject' => 'Mening murojaatim']);
    Ticket::factory()->for($stranger)->create(['subject' => 'Boshqa murojaat']);

    Sanctum::actingAs($user);

    $this->getJson(route('api.tickets.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.subject', 'Mening murojaatim');
});

test('a stranger cannot view someone elses ticket', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $ticket = Ticket::factory()->for($owner)->create();

    Sanctum::actingAs($stranger);

    $this->getJson(route('api.tickets.show', $ticket))->assertForbidden();
});

test('a user can add a message and status stays open', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.tickets.messages.store', $ticket), [
        'message' => 'Qo\'shimcha savolim bor.',
    ]);

    $response->assertCreated()->assertJsonCount(1, 'data.messages');
    expect($ticket->fresh()->status)->toBe(Ticket::STATUS_OPEN);
});

test('a moderator reply marks the ticket as answered', function () {
    $user = User::factory()->create();
    $moderator = User::factory()->moderator()->create();
    $ticket = Ticket::factory()->for($user)->create();

    Sanctum::actingAs($moderator);

    $this->postJson(route('api.tickets.messages.store', $ticket), ['message' => 'Javob']);

    expect($ticket->fresh()->status)->toBe(Ticket::STATUS_ANSWERED);
});

test('a user can close their own ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    Sanctum::actingAs($user);

    $this->postJson(route('api.tickets.close', $ticket))
        ->assertOk()
        ->assertJsonPath('data.status', Ticket::STATUS_CLOSED);
});

test('closing an already closed ticket returns a 422', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->closed()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('api.tickets.close', $ticket))->assertStatus(422);
});
