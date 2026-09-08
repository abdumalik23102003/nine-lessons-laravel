<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\AdvertController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryController::class, 'index'])
    ->name('api.categories.index');

Route::get('regions', [RegionController::class, 'index'])->name('api.regions.index');

Route::get('adverts', [AdvertController::class, 'index'])->name('api.adverts.index');

Route::get('adverts/{advert}', [AdvertController::class, 'show'])->name('api.adverts.show');
