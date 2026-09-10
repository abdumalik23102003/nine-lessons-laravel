<?php

use App\Http\Controllers\Api\AdvertController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryController::class, 'index'])
    ->name('api.categories.index');

Route::get('regions', [RegionController::class, 'index'])->name('api.regions.index');

Route::get('adverts', [AdvertController::class, 'index'])->name('api.adverts.index');

Route::get('adverts/{advert}', [AdvertController::class, 'show'])->name('api.adverts.show');

// Ochiq (token talab qilmaydi) — hozircha hech kim emasman, token so'rayapman.
Route::post('register', [AuthController::class, 'register'])->name('api.register');
Route::post('login', [AuthController::class, 'login'])->name('api.login');

// Himoyalangan (auth:sanctum) — to'g'ri token bo'lishi shart.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('user', [AuthController::class, 'me'])->name('api.user');
});
