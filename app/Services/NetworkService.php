<?php

namespace App\Services;

use App\Models\Network;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class NetworkService
{
    public function handleCallback(string $provider, SocialiteUser $providerUser): array
    {
        return DB::transaction(function () use ($provider, $providerUser) {
            $network = Network::where('name', $provider)
                ->where('network_id', $providerUser->getId())
                ->first();

            if ($network) {
                $user = $network->user;

                // ★ suspended / deleted bloklash
                if ($user->isSuspended() || $user->isDeleted()) {
                    throw new \DomainException('Hisob bloklangan yoki o\'chirilgan.');
                }

                $token = $user->createToken('mobile')->plainTextToken;

                return [
                    'user' => $user,
                    'token' => $token,
                    'is_new' => false,
                ];
            }

            $user = User::where('email', $providerUser->getEmail())->first();

            if ($user) {
                if ($user->isSuspended() || $user->isDeleted()) {
                    throw new \DomainException('Hisob bloklangan yoki o\'chirilgan.');
                }

                Network::fromProviderUser($user, $provider, $providerUser);
                $token = $user->createToken('mobile')->plainTextToken;

                return [
                    'user' => $user,
                    'token' => $token,
                    'is_new' => false,
                ];
            }

            // ★ Yangi social user — darhol active (kursdagi kabi)
            $user = User::create([
                'name' => $providerUser->getName() ?? 'User',
                'email' => $providerUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'status' => User::STATUS_ACTIVE,
                'activated_at' => now(),
                'email_verified_at' => now(), // provider tasdiqlagan
            ]);

            Network::fromProviderUser($user, $provider, $providerUser);

            $token = $user->createToken('mobile')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
                'is_new' => true,
            ];
        });
    }
    public function unlinkNetwork(User $user, string $provider): void
    {
        $user->networks()
            ->where('name', $provider)
            ->delete();
    }

    public function hasNetwork(User $user, string $provider): bool
    {
        return $user->networks()
            ->where('name', $provider)
            ->exists();
    }
}
