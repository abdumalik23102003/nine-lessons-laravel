<?php

namespace App\Services;

use App\Events\AdvertWasChanged;
use App\Models\Advert;
use App\Models\User;
use App\Notifications\AdvertModerationApprovedNotification;
use App\Notifications\AdvertRejectedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvertService
{
    public function create(User $user, array $data): Advert
    {
        return DB::transaction(function () use ($user, $data) {
            $advert = Advert::create([
                ...$data,
                'user_id' => $user->id,
                'status' => Advert::STATUS_DRAFT,
            ]);

            AdvertWasChanged::dispatch($advert, 'created');
            return $advert;
        });
    }

    public function update(Advert $advert, array $data): Advert
    {
        return DB::transaction(function () use ($advert, $data) {
            $advert->update($data);
            AdvertWasChanged::dispatch($advert, 'updated');
            return $advert;
        });
    }

    public function addPhotos(Advert $advert, array $photos): void
    {
        foreach ($photos as $photo) {
            $advert->photos()->create(['file' => $photo]);
        }
    }

    public function removePhoto(Advert $advert, int $photoId): void
    {
        $photo = $advert->photos()->findOrFail($photoId);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->file);
        $photo->delete();
    }

    public function sendToModeration(Advert $advert): Advert
    {
        return DB::transaction(function () use ($advert) {
            $advert->sendToModeration();
            AdvertWasChanged::dispatch($advert, 'updated');
            return $advert->fresh();
        });
    }

    public function moderate(Advert $advert, Carbon $expiresAt): Advert
    {
        return DB::transaction(function () use ($advert, $expiresAt) {
            $advert->moderate($expiresAt);
            $advert->user->notify(new AdvertModerationApprovedNotification($advert));
            AdvertWasChanged::dispatch($advert, 'moderated');
            return $advert->fresh();
        });
    }

    public function reject(Advert $advert, string $reason): Advert
    {
        return DB::transaction(function () use ($advert, $reason) {
            $advert->reject($reason);
            $advert->user->notify(new AdvertRejectedNotification($advert, $reason));
            AdvertWasChanged::dispatch($advert, 'updated');
            return $advert->fresh();
        });
    }

    public function close(Advert $advert): Advert
    {
        return DB::transaction(function () use ($advert) {
            $advert->close();
            AdvertWasChanged::dispatch($advert, 'updated');
            return $advert->fresh();
        });
    }

    public function delete(Advert $advert): bool
    {
        return DB::transaction(function () use ($advert) {
            foreach ($advert->photos as $photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->file);
            }
            
            AdvertWasChanged::dispatch($advert, 'deleted');
            return $advert->delete();
        });
    }
}
