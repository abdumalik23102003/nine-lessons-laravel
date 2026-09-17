<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\PhotoRequest;
use App\Http\Resources\PhotoResource;
use App\Models\Advert;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class AdvertPhotoController extends Controller
{
    #[OA\Post(
        path: '/api/adverts/{advert}/photos',
        summary: 'Upload advert photos',
        tags: ['Adverts'],
        parameters: [
            new OA\Parameter(
                name: 'advert',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'photos',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary')
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Photos uploaded successfully'
            ),
            new OA\Response(
                response: 403,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function store(PhotoRequest $request, Advert $advert): JsonResponse
    {
        $this->authorize('update', $advert);

        $photos = [];
        foreach ($request->file('photos', []) as $file) {
            $path = $file->store('adverts/' . now()->format('y/m/d'), 'public');
            
            $photo = $advert->photos()->create(['file' => $path]);
            $photos[] = new PhotoResource($photo);
        }

        return response()->json(['data' => $photos], 201);
    }

    #[OA\Delete(
        path: '/api/adverts/{advert}/photos/{photo}',
        summary: 'Delete advert photo',
        tags: ['Adverts'],
        parameters: [
            new OA\Parameter(
                name: 'advert',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'photo',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Photo deleted successfully'
            ),
            new OA\Response(
                response: 403,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function destroy(Advert $advert, Photo $photo): JsonResponse
    {
        $this->authorize('delete', $photo);

        // Verify photo belongs to advert
        if ($photo->advert_id !== $advert->id) {
            abort(404);
        }

        Storage::disk('public')->delete($photo->file);
        Photo::deleteEmptyDirectories($photo->file);
        
        $photo->delete();

        return response()->json(status: 204);
    }
}
