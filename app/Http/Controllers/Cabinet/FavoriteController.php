<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        return view('cabinet.favorites.index', [
            'adverts' => $request->user()
            ->favoriteAdverts()
            ->with(['category', 'region', 'photos'])
            ->latest('favorite_adverts.created_at')
            ->paginate(20),
        ]);
    }

    public function toggle(Request $request, Advert $advert): RedirectResponse
    {
        $user = $request->user();
        if ($user->hasFavorited($advert)) {
            $user->favoriteAdverts()->detach($advert);
        } else {
            $user->favoriteAdverts()->attach($advert);
        }

        return back();
    }
}
