<?php
namespace App\Policies;
use App\Models\Advert;
use App\Models\User;

class AdvertPolicy
{
    public function update(User $user, Advert $advert): bool
    {
        return $user->id === $advert->user_id;
    }
    public function view(?User $user, Advert $advert): bool
    {
        return $advert->isActive() || $user?->id === $advert->user_id;
    }
}
