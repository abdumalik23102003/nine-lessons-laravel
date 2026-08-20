<?php

use App\Models\Ticket;
use App\Models\User;

test('a user can create a ticket', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('cabinet.tickets.store'), [
            'subject' => 'Yordam kerak',
            'content' => 'Mening e\'lonim rad etildi, sababini bilmoqchiman.',
        ])
        ->assertRedirect();

    expect(Ticket::query()->where('user_id', $user->id)->where('status', Ticket::STATUS_OPEN)->exists())->toBeTrue();
});

test('a user cannot view another users ticket', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $ticket = Ticket::factory()->for($owner)->create();

    $this->actingAs($stranger)
        ->get(route('cabinet.tickets.show', $ticket))
        ->assertForbidden();
});

test('a moderator can view any users ticket', function () {
    $owner = User::factory()->create();
    $moderator = User::factory()->moderator()->create();
    $ticket = Ticket::factory()->for($owner)->create();

    $this->actingAs($moderator)
        ->get(route('cabinet.tickets.show', $ticket))
        ->assertOk();
});

test('a user can add a message and status stays open', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $this->actingAs($user)->post(route('cabinet.tickets.messages.store', $ticket), [
        'message' => 'Qo\'shimcha savolim bor.',
    ]);

    expect($ticket->fresh())
        ->status->toBe(Ticket::STATUS_OPEN)
        ->messages->toHaveCount(1);
});

test('an admin reply marks the ticket as answered', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $this->actingAs($admin)->post(route('admin.tickets.messages.store', $ticket), [
        'message' => 'Muammo hal qilindi.',
    ]);

    expect($ticket->fresh()->status)->toBe(Ticket::STATUS_ANSWERED);
});

test('a closed ticket does not accept new messages', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->closed()->for($user)->create();

    $this->actingAs($user)
        ->post(route('cabinet.tickets.messages.store', $ticket), ['message' => 'Salom'])
        ->assertRedirect();

    expect($ticket->fresh()->messages)->toHaveCount(0);
});

test('a user can close their own ticket', function () {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $this->actingAs($user)->post(route('cabinet.tickets.close', $ticket));

    expect($ticket->fresh()->status)->toBe(Ticket::STATUS_CLOSED);
});

test('a regular user cannot access the admin ticket list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.tickets.index'))->assertForbidden();
});
