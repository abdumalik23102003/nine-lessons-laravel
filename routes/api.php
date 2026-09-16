<?php

use App\Http\Controllers\Api\AdvertController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DialogController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryController::class, 'index'])
    ->name('api.categories.index');

Route::get('regions', [RegionController::class, 'index'])->name('api.regions.index');

Route::get('adverts', [AdvertController::class, 'index'])->name('api.adverts.index');

Route::get('adverts/{advert}', [AdvertController::class, 'show'])->name('api.adverts.show');

Route::get('pages', [PageController::class, 'index'])->name('api.pages.index');
Route::get('pages/{page:slug}', [PageController::class, 'show'])->name('api.pages.show');

// Ochiq (token talab qilmaydi) — hozircha hech kim emasman, token so'rayapman.
Route::post('register', [AuthController::class, 'register'])->name('api.register');
Route::post('login', [AuthController::class, 'login'])->name('api.login');

// Himoyalangan (auth:sanctum) — to'g'ri token bo'lishi shart.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('user', [AuthController::class, 'me'])->name('api.user');

    Route::get('cabinet/adverts', [AdvertController::class, 'myAdverts'])->name('api.cabinet.adverts.index');
    Route::post('adverts', [AdvertController::class, 'store'])->name('api.adverts.store');
    Route::patch('adverts/{advert}', [AdvertController::class, 'update'])->name('api.adverts.update');
    Route::delete('adverts/{advert}', [AdvertController::class, 'destroy'])->name('api.adverts.destroy');

    Route::get('tickets', [TicketController::class, 'index'])->name('api.tickets.index');
    Route::post('tickets', [TicketController::class, 'store'])->name('api.tickets.store');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('api.tickets.show');
    Route::post('tickets/{ticket}/messages', [TicketController::class, 'addMessage'])->name('api.tickets.messages.store');
    Route::post('tickets/{ticket}/close', [TicketController::class, 'close'])->name('api.tickets.close');

    Route::get('cabinet/banners', [BannerController::class, 'index'])->name('api.cabinet.banners.index');
    Route::post('banners', [BannerController::class, 'store'])->name('api.banners.store');
    Route::post('banners/{banner}', [BannerController::class, 'update'])->name('api.banners.update');
    Route::delete('banners/{banner}', [BannerController::class, 'destroy'])->name('api.banners.destroy');
    Route::post('banners/{banner}/send-to-moderation', [BannerController::class, 'sendToModeration'])->name('api.banners.send-to-moderation');

    Route::get('favorites', [FavoriteController::class, 'index'])->name('api.favorites.index');
    Route::post('adverts/{advert}/favorite', [FavoriteController::class, 'toggle'])->name('api.favorites.toggle');

    Route::get('dialogs', [DialogController::class, 'index'])->name('api.dialogs.index');
    Route::get('dialogs/{dialog}', [DialogController::class, 'show'])->name('api.dialogs.show');
    Route::post('dialogs/{dialog}/messages', [DialogController::class, 'addMessage'])->name('api.dialogs.messages.store');
    Route::post('adverts/{advert}/dialogs', [DialogController::class, 'start'])->name('api.dialogs.start');
});
