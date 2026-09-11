<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\SearchRequest;
use App\Http\Requests\Cabinet\Adverts\AdvertRequest;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use App\Services\Search\AdvertSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function myAdverts(Request $request): AnonymousResourceCollection
    {
        $adverts = Advert::forUser($request->user())
            ->with(['category', 'region', 'photos'])
            ->latest()
            ->paginate(20);

        return AdvertResource::collection($adverts);
    }

    public function store(AdvertRequest $request): JsonResponse
    {
        $advert = Advert::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return (new AdvertResource($advert->load(['category', 'region', 'photos'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(AdvertRequest $request, Advert $advert): AdvertResource
    {
        $this->authorize('update', $advert);

        $advert->update($request->validated());

        return new AdvertResource($advert->load(['category', 'region', 'photos']));
    }

    public function destroy(Advert $advert): JsonResponse
    {
        $this->authorize('update', $advert);

        $advert->delete();

        return response()->json(status: 204);
    }
}
