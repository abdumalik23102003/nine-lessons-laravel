<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $adverts = $request->user()
            ->favoriteAdverts()
            ->with(['category', 'region', 'photos'])
            ->latest('favorite_adverts.created_at')
            ->paginate(20);

        return AdvertResource::collection($adverts);
    }

    public function toggle(Request $request, Advert $advert): JsonResponse
    {
        $user = $request->user();

        if ($user->hasFavorited($advert)) {
            $user->favoriteAdverts()->detach($advert);
            $favorited = false;
        } else {
            $user->favoriteAdverts()->attach($advert);
            $favorited = true;
        }

        return response()->json(['data' => ['favorited' => $favorited]]);
    }
}
