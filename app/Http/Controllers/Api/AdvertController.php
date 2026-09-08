<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\SearchRequest;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use App\Services\Search\AdvertSearchService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdvertController extends Controller
{
    public function index(SearchRequest $request, AdvertSearchService $service): AnonymousResourceCollection
    {
        $adverts = $service->search($request);
        return AdvertResource::collection($adverts);
    }

    public function show(Advert $advert): AdvertResource
    {
        $this->authorize('view', $advert);
        return new AdvertResource($advert->load(['category', 'region', 'photos']));
    }
}
