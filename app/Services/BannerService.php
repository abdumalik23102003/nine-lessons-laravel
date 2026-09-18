<?php

namespace App\Services;

use App\Events\BannerWasChanged;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    public function create(User $user, array $data): Banner
    {
        return DB::transaction(function () use ($user, $data) {
            $banner = Banner::create([
                ...$data,
                'user_id' => $user->id,
                'status' => Banner::STATUS_DRAFT,
            ]);

            BannerWasChanged::dispatch($banner, 'created');
            return $banner;
        });
    }

    public function update(Banner $banner, array $data): Banner
    {
        return DB::transaction(function () use ($banner, $data) {
            $banner->update($data);
            BannerWasChanged::dispatch($banner, 'updated');
            return $banner;
        });
    }

    public function sendToModeration(Banner $banner): Banner
    {
        return DB::transaction(function () use ($banner) {
            $banner->sendToModeration();
            BannerWasChanged::dispatch($banner, 'updated');
            return $banner->fresh();
        });
    }

    public function delete(Banner $banner): bool
    {
        return DB::transaction(function () use ($banner) {
            if ($banner->file) {
                Storage::disk('public')->delete($banner->file);
            }

            BannerWasChanged::dispatch($banner, 'deleted');
            return $banner->delete();
        });
    }
}
