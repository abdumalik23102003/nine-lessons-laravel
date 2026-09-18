<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Banners\BannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Services\BannerService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $banners = Banner::forUser($request->user())->latest()->paginate(20);

        return BannerResource::collection($banners);
    }

    public function store(BannerRequest $request, BannerService $service): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('banners/' . now()->format('y/m/d'), 'public');
        }

        $banner = $service->create($request->user(), $data);

        return (new BannerResource($banner))->response()->setStatusCode(201);
    }

    public function update(BannerRequest $request, Banner $banner, BannerService $service): BannerResource
    {
        $this->authorize('update', $banner);
        
        $data = $request->validated();
        
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($banner->file);
            $data['file'] = $request->file('file')->store('banners/' . now()->format('y/m/d'), 'public');
        }

        $banner = $service->update($banner, $data);

        return new BannerResource($banner);
    }

    public function destroy(Banner $banner, BannerService $service): JsonResponse
    {
        $this->authorize('update', $banner);
        $service->delete($banner);
        
        return response()->json(status: 204);
    }

    public function sendToModeration(Banner $banner, BannerService $service): BannerResource|JsonResponse
    {
        $this->authorize('update', $banner);

        try {
            $banner = $service->sendToModeration($banner);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return new BannerResource($banner);
    }
}
