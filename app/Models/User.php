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

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_ADMIN = 'admin';

    public static function rolesList(): array
    {
        return [
            self::ROLE_USER => 'Foydalanuvchi',
            self::ROLE_MODERATOR => 'Moderator',
            self::ROLE_ADMIN => 'Admin',
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

    public function favoriteAdverts(): BelongsToMany
    {
        return $this->belongsToMany(Advert::class, 'favorite_adverts')->withTimestamps();
    }

    public function hasFavorited(Advert $advert): bool
    {
        return $this->favoriteAdverts()->where('advert_id', $advert->id)->exists();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
