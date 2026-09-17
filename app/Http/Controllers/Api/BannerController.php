<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Banners\BannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
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

    public function store(BannerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = Banner::STATUS_DRAFT;

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('banners/' . now()->format('y/m/d'), 'public');
        }

        $banner = Banner::create($data);

        return (new BannerResource($banner))->response()->setStatusCode(201);
    }

    public function update(BannerRequest $request, Banner $banner): BannerResource
    {
        $this->authorize('update', $banner);
        
        $data = $request->validated();
        
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($banner->file);
            $data['file'] = $request->file('file')->store('banners/' . now()->format('y/m/d'), 'public');
        }

        $banner->update($data);

        return new BannerResource($banner);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $this->authorize('update', $banner);

        if ($banner->file) {
            Storage::disk('public')->delete($banner->file);
        }
        $banner->delete();
        return response()->json(status: 204);
    }

    public function sendToModeration(Banner $banner): BannerResource|JsonResponse
    {
        $this->authorize('update', $banner);

        try {
            $banner->sendToModeration();
        } catch (DomainException $e) {
            return response()->json(['message' => e($e->getMessage())], 422);
        }

        return new BannerResource($banner);
    }
}
