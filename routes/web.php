<?php

use App\Http\Controllers\Admin\Adverts\CategoryController;
use App\Http\Controllers\Admin\Banners\ModerationController as BannerModerationController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\Adverts\ModerationController;
use App\Http\Controllers\Adverts\AdvertController as PublicAdvertController;
use App\Http\Controllers\BannerClickController;
use App\Http\Controllers\Cabinet\Adverts\AdvertController;
use App\Http\Controllers\Cabinet\Adverts\AttributeController;
use App\Http\Controllers\Cabinet\Adverts\PhotoController;
use App\Http\Controllers\Cabinet\Banners\BannerController;
use App\Http\Controllers\Cabinet\FavoriteController;
use App\Http\Controllers\Cabinet\TicketController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('adverts', [PublicAdvertController::class, 'index'])->name('adverts.index');
Route::get('adverts/{advert}', [PublicAdvertController::class, 'show'])->name('adverts.show');

Route::get('banners/{banner}/go', BannerClickController::class)->name('banners.click');

Route::get('pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('cabinet')->name('cabinet.')->group(function () {
    Route::resource('adverts', AdvertController::class)->except(['show']);
    Route::post('adverts/{advert}/photos', [PhotoController::class, 'store'])->name('adverts.photos.store');
    Route::delete('adverts/{advert}/photos/{photo}', [PhotoController::class, 'destroy'])->name('adverts.photos.destroy');
    Route::put('adverts/{advert}/attributes', [AttributeController::class, 'update'])->name('adverts.attributes.update');
    Route::post('adverts/{advert}/send-to-moderation', [AdvertController::class, 'sendToModeration'])->name('adverts.send-to-moderation');

    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('tickets/{ticket}/messages', [TicketController::class, 'addMessage'])->name('tickets.messages.store');
    Route::post('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');

    Route::resource('banners', BannerController::class)->except(['show']);
    Route::post('banners/{banner}/send-to-moderation', [BannerController::class, 'sendToModeration'])->name('banners.send-to-moderation');

    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('adverts/{advert}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
});

Route::middleware(['auth', 'moderator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('moderation', [ModerationController::class, 'index'])->name('moderation.index');
    Route::post('moderation/{advert}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
    Route::post('moderation/{advert}/reject', [ModerationController::class, 'reject'])->name('moderation.reject');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('users', UsersController::class)->only(['index', 'edit', 'update', 'destroy']);

    Route::resource('tickets', AdminTicketController::class)->only(['index', 'show']);
    Route::post('tickets/{ticket}/messages', [AdminTicketController::class, 'addMessage'])->name('tickets.messages.store');
    Route::post('tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');

    Route::get('banners/moderation', [BannerModerationController::class, 'index'])->name('banners.moderation.index');
    Route::post('banners/moderation/{banner}/approve', [BannerModerationController::class, 'approve'])->name('banners.moderation.approve');
    Route::post('banners/moderation/{banner}/reject', [BannerModerationController::class, 'reject'])->name('banners.moderation.reject');

    Route::resource('pages', AdminPageController::class)->except(['show']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('regions', RegionController::class)->except(['show']);
});
require __DIR__ . '/auth.php';
