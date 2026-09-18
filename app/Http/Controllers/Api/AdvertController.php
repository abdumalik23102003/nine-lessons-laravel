<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\SearchRequest;
use App\Http\Requests\Cabinet\Adverts\AdvertRequest;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use App\Services\AdvertService;
use App\Services\Search\AdvertSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

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
        return new AdvertResource($advert->load(['category', 'region', 'photos', 'values.attribute']));
    }

    public function myAdverts(Request $request): AnonymousResourceCollection
    {
        $adverts = Advert::forUser($request->user())
            ->with(['category', 'region', 'photos', 'values.attribute'])
            ->latest()
            ->paginate(20);

        return AdvertResource::collection($adverts);
    }

    public function store(AdvertRequest $request, AdvertService $service): JsonResponse
    {
        $advert = $service->create($request->user(), $request->validated());
        $advert->load(['category', 'region', 'photos', 'values.attribute']);

        return (new AdvertResource($advert))
            ->response()
            ->setStatusCode(201);
    }

    public function update(AdvertRequest $request, Advert $advert, AdvertService $service): AdvertResource
    {
        $this->authorize('update', $advert);
        $service->update($advert, $request->validated());
        $advert->load(['category', 'region', 'photos', 'values.attribute']);

        return new AdvertResource($advert);
    }

    public function destroy(Advert $advert, AdvertService $service): JsonResponse
    {
        $this->authorize('update', $advert);
        $service->delete($advert);

        return response()->json(status: 204);
    }

    public function sendToModeration(Advert $advert, AdvertService $service)
    {
        $this->authorize('update', $advert);

        try {
            $advert = $service->sendToModeration($advert);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new AdvertResource($advert->load(['category', 'region', 'photos', 'values.attribute']));
    }

    public function close(Advert $advert, AdvertService $service)
    {
        $this->authorize('update', $advert);

        try {
            $advert = $service->close($advert);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new AdvertResource($advert->load(['category', 'region', 'photos', 'values.attribute']));
    }
}
