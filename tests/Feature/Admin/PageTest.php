<?php

use App\Models\Page;
use App\Models\User;

test('anyone can view a published page', function () {
    $page = Page::factory()->create(['title' => 'Biz haqimizda', 'slug' => 'biz-haqimizda']);

    $this->get(route('pages.show', $page))
        ->assertOk()
        ->assertSee('Biz haqimizda');
});

test('a page shown in menu appears in the footer', function () {
    $page = Page::factory()->inMenu()->create(['title' => 'Qoidalar']);

    $this->get(route('home'))->assertSee('Qoidalar');
});

test('a page not shown in menu does not appear in the footer', function () {
    Page::factory()->create(['title' => 'Yashirin sahifa', 'show_in_menu' => false]);

    $this->get(route('home'))->assertDontSee('Yashirin sahifa');
});

test('guests cannot access page management', function () {
    $this->get(route('admin.pages.index'))->assertRedirect(route('login'));
});

test('a regular user cannot access page management', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.pages.index'))->assertForbidden();
});

test('a moderator can create a page', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->post(route('admin.pages.store'), [
            'title' => 'Yordam',
            'slug' => 'yordam',
            'content' => 'Yordam matni.',
        ])
        ->assertRedirect(route('admin.pages.index'));

    expect(Page::query()->where('slug', 'yordam')->exists())->toBeTrue();
});

test('page slug must be unique', function () {
    $moderator = User::factory()->moderator()->create();
    Page::factory()->create(['slug' => 'yordam']);

    $this->actingAs($moderator)
        ->post(route('admin.pages.store'), [
            'title' => 'Boshqa sarlavha',
            'slug' => 'yordam',
            'content' => 'Matn.',
        ])
        ->assertSessionHasErrors('slug');
});

test('a moderator can update and delete a page', function () {
    $moderator = User::factory()->moderator()->create();
    $page = Page::factory()->create();

    $this->actingAs($moderator)->put(route('admin.pages.update', $page), [
        'title' => 'Yangilangan sarlavha',
        'slug' => $page->slug,
        'content' => $page->content,
    ]);

    expect($page->fresh()->title)->toBe('Yangilangan sarlavha');

    $this->actingAs($moderator)->delete(route('admin.pages.destroy', $page));

    expect(Page::query()->find($page->id))->toBeNull();
});
