<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'phone_verification_token', 'phone_verified_at', 'status', 'activated_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_ADMIN = 'admin';

    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_DELETED = 'deleted';

    public static function rolesList(): array
    {
        return [
            self::ROLE_USER => 'Foydalanuvchi',
            self::ROLE_MODERATOR => 'Moderator',
            self::ROLE_ADMIN => 'Admin',
        ];
    }

    public static function statusesList(): array
    {
        return [
            self::STATUS_WAIT => 'Kutmoqda',
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_SUSPENDED => 'Bloklangan',
            self::STATUS_DELETED => 'O\'chirilgan',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    public function canModerate(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isWaiting(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isDeleted(): bool
    {
        return $this->status === self::STATUS_DELETED;
    }

    public function activate(): void
    {
        if ($this->status !== self::STATUS_WAIT) {
            throw new \DomainException('Only waiting users can be activated.');
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'activated_at' => now(),
        ]);
    }

    public function suspend(): void
    {
        if ($this->status === self::STATUS_DELETED) {
            throw new \DomainException('Cannot suspend deleted user.');
        }

        $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    public function unsuspend(): void
    {
        if ($this->status !== self::STATUS_SUSPENDED) {
            throw new \DomainException('Only suspended users can be unsuspended.');
        }

        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function delete(): bool
    {
        return $this->update(['status' => self::STATUS_DELETED]) && parent::delete();
    }

    public function favoriteAdverts(): BelongsToMany
    {
        return $this->belongsToMany(Advert::class, 'favorite_adverts')->withTimestamps();
    }

    public function hasFavorited(Advert $advert): bool
    {
        return $this->favoriteAdverts()->where('advert_id', $advert->id)->exists();
    }

    public function networks(): HasMany
    {
        return $this->hasMany(Network::class);
    }

    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function generatePhoneVerificationToken(): string
    {
        $token = Str::random(6);
        $this->update([
            'phone_verification_token' => $token,
        ]);
        return $token;
    }

    public function verifyPhone(string $token): bool
    {
        if ($this->phone_verification_token === $token) {
            $this->update([
                'phone_verified_at' => now(),
                'phone_verification_token' => null,
            ]);
            return true;
        }
        return false;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'activated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
