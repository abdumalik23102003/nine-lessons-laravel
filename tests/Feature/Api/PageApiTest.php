<?php

use App\Models\Page;

test('index returns only pages shown in menu', function () {
    Page::factory()->inMenu()->create(['title' => 'Qoidalar']);
    Page::factory()->create(['title' => 'Yashirin', 'show_in_menu' => false]);

    $response = $this->getJson(route('api.pages.index'));

    $titles = collect($response->json('data'))->pluck('title')->all();

    expect($titles)->toContain('Qoidalar')->not->toContain('Yashirin');
});

test('show returns a page by its slug', function () {
    $page = Page::factory()->create(['title' => 'Biz haqimizda', 'slug' => 'biz-haqimizda']);

    $this->getJson(route('api.pages.show', $page))
        ->assertOk()
        ->assertJsonPath('data.title', 'Biz haqimizda');
});

test('show returns 404 for an unknown slug', function () {
    $this->getJson('/api/pages/no-such-page')->assertNotFound();
});
