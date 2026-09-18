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
            // Check if network already registered
            $network = Network::where('name', $provider)
                ->where('network_id', $providerUser->getId())
                ->first();

            if ($network) {
                // Existing user via social
                $user = $network->user;
                $token = $user->createToken('mobile')->plainTextToken;

                return [
                    'user' => $user,
                    'token' => $token,
                    'is_new' => false,
                ];
            }

            // Check if user with this email exists
            $user = User::where('email', $providerUser->getEmail())->first();

            if ($user) {
                // Link social network to existing account
                Network::fromProviderUser($user, $provider, $providerUser);
                $token = $user->createToken('mobile')->plainTextToken;

                return [
                    'user' => $user,
                    'token' => $token,
                    'is_new' => false,
                ];
            }

            // Create new user
            $user = User::create([
                'name' => $providerUser->getName() ?? 'User',
                'email' => $providerUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'status' => User::STATUS_WAIT, // New users start as waiting
            ]);

            // Link social network
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
