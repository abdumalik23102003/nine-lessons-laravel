<?php

use App\Models\Advert;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Dialog;
use App\Models\Page;
use App\Models\Region;
use App\Models\Ticket;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator;

// Home
Breadcrumbs::for('home', function (Generator $trail) {
    $trail->push('Bosh sahifa', route('home'));
});

// Home > Dashboard
Breadcrumbs::for('dashboard', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});

// Home > Profile
Breadcrumbs::for('profile.edit', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Profil', route('profile.edit'));
});

// Home > Pages > Page
Breadcrumbs::for('pages.show', function (Generator $trail, Page $page) {
    $trail->parent('home');
    $trail->push($page->title, route('pages.show', $page));
});

/*
 |--------------------------------------------------------------------------
 | Public Adverts
 |--------------------------------------------------------------------------
 */

// Home > Adverts
Breadcrumbs::for('adverts.index', function (Generator $trail) {
    $trail->parent('home');
    $trail->push("E'lonlar", route('adverts.index'));
});

// Home > Adverts > Advert
Breadcrumbs::for('adverts.show', function (Generator $trail, Advert $advert) {
    $trail->parent('adverts.index');
    $trail->push($advert->title, route('adverts.show', $advert));
});

/*
 |--------------------------------------------------------------------------
 | Cabinet > Adverts
 |--------------------------------------------------------------------------
 */

// Dashboard > My Adverts
Breadcrumbs::for('cabinet.adverts.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push("Mening e'lonlarim", route('cabinet.adverts.index'));
});

// Dashboard > My Adverts > New
Breadcrumbs::for('cabinet.adverts.create', function (Generator $trail) {
    $trail->parent('cabinet.adverts.index');
    $trail->push("Yangi e'lon", route('cabinet.adverts.create'));
});

// Dashboard > My Adverts > Edit
Breadcrumbs::for('cabinet.adverts.edit', function (Generator $trail, Advert $advert) {
    $trail->parent('cabinet.adverts.index');
    $trail->push($advert->title, route('cabinet.adverts.edit', $advert));
});

/*
 |--------------------------------------------------------------------------
 | Cabinet > Tickets
 |--------------------------------------------------------------------------
 */

// Dashboard > Tickets
Breadcrumbs::for('cabinet.tickets.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Murojaatlarim', route('cabinet.tickets.index'));
});

// Dashboard > Tickets > New
Breadcrumbs::for('cabinet.tickets.create', function (Generator $trail) {
    $trail->parent('cabinet.tickets.index');
    $trail->push('Yangi murojaat', route('cabinet.tickets.create'));
});

// Dashboard > Tickets > Ticket
Breadcrumbs::for('cabinet.tickets.show', function (Generator $trail, Ticket $ticket) {
    $trail->parent('cabinet.tickets.index');
    $trail->push($ticket->subject, route('cabinet.tickets.show', $ticket));
});

/*
 |--------------------------------------------------------------------------
 | Cabinet > Banners
 |--------------------------------------------------------------------------
 */

// Dashboard > My Banners
Breadcrumbs::for('cabinet.banners.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Bannerlarim', route('cabinet.banners.index'));
});

// Dashboard > My Banners > New
Breadcrumbs::for('cabinet.banners.create', function (Generator $trail) {
    $trail->parent('cabinet.banners.index');
    $trail->push('Yangi banner', route('cabinet.banners.create'));
});

// Dashboard > My Banners > Edit
Breadcrumbs::for('cabinet.banners.edit', function (Generator $trail, Banner $banner) {
    $trail->parent('cabinet.banners.index');
    $trail->push($banner->name, route('cabinet.banners.edit', $banner));
});

/*
 |--------------------------------------------------------------------------
 | Cabinet > Favorites
 |--------------------------------------------------------------------------
 */

// Dashboard > Favorites
Breadcrumbs::for('cabinet.favorites.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Sevimlilar', route('cabinet.favorites.index'));
});

/*
 |--------------------------------------------------------------------------
 | Cabinet > Dialogs
 |--------------------------------------------------------------------------
 */

// Dashboard > Dialogs
Breadcrumbs::for('cabinet.dialogs.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Xabarlar', route('cabinet.dialogs.index'));
});

// Dashboard > Dialogs > Dialog
Breadcrumbs::for('cabinet.dialogs.show', function (Generator $trail, Dialog $dialog) {
    $trail->parent('cabinet.dialogs.index');
    $trail->push($dialog->advert->title, route('cabinet.dialogs.show', $dialog));
});

/*
 |--------------------------------------------------------------------------
 | Admin > Moderation
 |--------------------------------------------------------------------------
 */

// Dashboard > Moderation
Breadcrumbs::for('admin.moderation.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push("E'lonlar moderatsiyasi", route('admin.moderation.index'));
});

// Dashboard > Banner Moderation
Breadcrumbs::for('admin.banners.moderation.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Bannerlar moderatsiyasi', route('admin.banners.moderation.index'));
});

/*
 |--------------------------------------------------------------------------
 | Admin > Categories
 |--------------------------------------------------------------------------
 */

// Dashboard > Categories
Breadcrumbs::for('admin.categories.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Kategoriyalar', route('admin.categories.index'));
});

// Dashboard > Categories > New
Breadcrumbs::for('admin.categories.create', function (Generator $trail) {
    $trail->parent('admin.categories.index');
    $trail->push('Yangi kategoriya', route('admin.categories.create'));
});

// Dashboard > Categories > Edit
Breadcrumbs::for('admin.categories.edit', function (Generator $trail, Category $category) {
    $trail->parent('admin.categories.index');
    $trail->push($category->name, route('admin.categories.edit', $category));
});

/*
 |--------------------------------------------------------------------------
 | Admin > Regions
 |--------------------------------------------------------------------------
 */

// Dashboard > Regions
Breadcrumbs::for('admin.regions.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Hududlar', route('admin.regions.index'));
});

// Dashboard > Regions > New
Breadcrumbs::for('admin.regions.create', function (Generator $trail) {
    $trail->parent('admin.regions.index');
    $trail->push('Yangi hudud', route('admin.regions.create'));
});

// Dashboard > Regions > Edit
Breadcrumbs::for('admin.regions.edit', function (Generator $trail, Region $region) {
    $trail->parent('admin.regions.index');
    $trail->push($region->name, route('admin.regions.edit', $region));
});

/*
 |--------------------------------------------------------------------------
 | Admin > Users
 |--------------------------------------------------------------------------
 */

// Dashboard > Users
Breadcrumbs::for('admin.users.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Foydalanuvchilar', route('admin.users.index'));
});

// Dashboard > Users > Edit
Breadcrumbs::for('admin.users.edit', function (Generator $trail, User $user) {
    $trail->parent('admin.users.index');
    $trail->push($user->name, route('admin.users.edit', $user));
});

/*
 |--------------------------------------------------------------------------
 | Admin > Tickets
 |--------------------------------------------------------------------------
 */

// Dashboard > Tickets (Admin)
Breadcrumbs::for('admin.tickets.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Murojaatlar', route('admin.tickets.index'));
});

// Dashboard > Tickets (Admin) > Ticket
Breadcrumbs::for('admin.tickets.show', function (Generator $trail, Ticket $ticket) {
    $trail->parent('admin.tickets.index');
    $trail->push($ticket->subject, route('admin.tickets.show', $ticket));
});

/*
 |--------------------------------------------------------------------------
 | Admin > Pages
 |--------------------------------------------------------------------------
 */

// Dashboard > Pages
Breadcrumbs::for('admin.pages.index', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Sahifalar', route('admin.pages.index'));
});

// Dashboard > Pages > New
Breadcrumbs::for('admin.pages.create', function (Generator $trail) {
    $trail->parent('admin.pages.index');
    $trail->push('Yangi sahifa', route('admin.pages.create'));
});

// Dashboard > Pages > Edit
Breadcrumbs::for('admin.pages.edit', function (Generator $trail, Page $page) {
    $trail->parent('admin.pages.index');
    $trail->push($page->title, route('admin.pages.edit', $page));
});
