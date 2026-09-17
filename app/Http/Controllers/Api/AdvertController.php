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
use OpenApi\Attributes as OA;

class AdvertController extends Controller
{
    #[OA\Get(
        path: '/api/adverts',
        summary: 'Get adverts',
        tags: ['Adverts'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of adverts'
            )
        ]
    )]
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

    public function store(AdvertRequest $request): JsonResponse
    {
        $advert = Advert::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return (new AdvertResource($advert->load(['category', 'region', 'photos', 'values.attribute'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(AdvertRequest $request, Advert $advert): AdvertResource
    {
        $this->authorize('update', $advert);

        $advert->update($request->validated());

        return new AdvertResource($advert->load(['category', 'region', 'photos', 'values.attribute']));
    }

    public function destroy(Advert $advert): JsonResponse
    {
        $this->authorize('update', $advert);

        $advert->delete();

        return response()->json(status: 204);
    }

    #[OA\Post(
        path: '/api/adverts/{advert}/send-to-moderation',
        summary: 'Send advert to moderation',
        tags: ['Adverts'],
        parameters: [
            new OA\Parameter(
                name: 'advert',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Advert sent to moderation'
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid request'
            ),
        ]
    )]
    public function sendToModeration(Advert $advert)
    {
        $this->authorize('update', $advert);

        try {
            $advert->sendToModeration();
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new AdvertResource($advert->load(['category', 'region', 'photos', 'values.attribute']));
    }

    #[OA\Post(
        path: '/api/adverts/{advert}/close',
        summary: 'Close advert',
        tags: ['Adverts'],
        parameters: [
            new OA\Parameter(
                name: 'advert',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Advert closed'
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid request'
            ),
        ]
    )]
    public function close(Advert $advert)
    {
        $this->authorize('update', $advert);

        try {
            $advert->close();
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new AdvertResource($advert->load(['category', 'region', 'photos', 'values.attribute']));
    }
}
