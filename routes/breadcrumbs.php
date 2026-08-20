<?php

use App\Models\Advert;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator;

// Home
Breadcrumbs::for('home', function (Generator $trail) {
    $trail->push('Home', route('home'));
});

// Dashboard > Dashboard
Breadcrumbs::for('dashboard', function (Generator $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});

// Profile > Edit
Breadcrumbs::for('profile.edit', function (Generator $trail) {
    $trail->parent('dashboard');
    $trail->push('Profile', route('profile.edit'));
});
