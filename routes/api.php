<?php

use App\Http\Controllers\Api\AdvertController;
use App\Http\Controllers\Api\AdvertPhotoController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DialogController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\NotificationController;
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

// Social login routes
Route::get('auth/{provider}/redirect', [AuthController::class, 'socialiteRedirect'])->name('api.auth.social.redirect');
Route::get('auth/{provider}/callback', [AuthController::class, 'socialiteCallback'])->name('api.auth.social.callback');

// Himoyalangan (auth:sanctum) — to'g'ri token bo'lishi shart.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('user', [AuthController::class, 'me'])->name('api.user');
    Route::put('user', [AuthController::class, 'update'])->name('api.user.update');

    Route::delete('auth/{provider}/unlink', [AuthController::class, 'unlinkNetwork'])->name('api.auth.network.unlink');

    Route::post('phone/request-verification', [AuthController::class, 'requestPhoneVerification'])->name('api.phone.request-verification');
    Route::post('phone/verify', [AuthController::class, 'verifyPhone'])->name('api.phone.verify');

    Route::get('notifications', [NotificationController::class, 'index'])->name('api.notifications.index');    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');

    Route::get('cabinet/adverts', [AdvertController::class, 'myAdverts'])->name('api.cabinet.adverts.index');
    Route::post('adverts', [AdvertController::class, 'store'])->name('api.adverts.store');
    Route::patch('adverts/{advert}', [AdvertController::class, 'update'])->name('api.adverts.update');
    Route::delete('adverts/{advert}', [AdvertController::class, 'destroy'])->name('api.adverts.destroy');
    Route::post('adverts/{advert}/send-to-moderation', [AdvertController::class, 'sendToModeration'])->name('api.adverts.send-to-moderation');
    Route::post('adverts/{advert}/close', [AdvertController::class, 'close'])->name('api.adverts.close');
    
    Route::post('adverts/{advert}/photos', [AdvertPhotoController::class, 'store'])->name('api.adverts.photos.store');
    Route::delete('adverts/{advert}/photos/{photo}', [AdvertPhotoController::class, 'destroy'])->name('api.adverts.photos.destroy');

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
