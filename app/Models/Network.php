<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Network extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'network_id', 'data'];

    protected $casts = [
        'data' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function findByProvider(string $provider, string $networkId): ?self
    {
        return static::where('name', $provider)
            ->where('network_id', $networkId)
            ->first();
    }

    public static function fromProviderUser(User $user, string $provider, $providerUser): self
    {
        return static::updateOrCreate(
            [
                'user_id' => $user->id,
                'name' => $provider,
                'network_id' => $providerUser->getId(),
            ],
            [
                'data' => [
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'avatar' => $providerUser->getAvatar(),
                ],
            ]
        );
    }
}
