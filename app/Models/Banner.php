<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_MODERATION = 'moderation';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public static function statusesList(): array
    {
        return [
            self::STATUS_DRAFT => 'Qoralama',
            self::STATUS_MODERATION => 'Moderatsiyada',
            self::STATUS_ACTIVE => 'Faol',
            self::STATUS_CLOSED => 'Yopilgan',
        ];
    }

    public function sendToModeration(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            throw new \DomainException('Banner qoralama holatida emas.');
        }
        if (! $this->file) {
            throw new \DomainException('Banner rasmini yuklang.');
        }

        $this->update(['status' => self::STATUS_MODERATION, 'reject_reason' => null]);
    }

    public function moderate(Carbon $expiresAt): void
    {
        if ($this->status !== self::STATUS_MODERATION) {
            throw new \DomainException('Banner moderatsiyaga yuborilmagan.');
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'published_at' => now(),
            'expires_at' => $expiresAt,
        ]);
    }

    public function reject(string $reason): void
    {
        if ($this->status !== self::STATUS_MODERATION) {
            throw new \DomainException('Banner moderatsiyaga yuborilmagan.');
        }

        $this->update(['status' => self::STATUS_DRAFT, 'reject_reason' => $reason]);
    }

    public function close(): void
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            throw new \DomainException('Banner faol emas.');
        }

        $this->update(['status' => self::STATUS_CLOSED]);
    }

    public function recordView(): void
    {
        $this->increment('views');
    }

    public function recordClick(): void
    {
        $this->increment('clicks');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getFileUrl(): ?string
    {
        return $this->file ? asset('storage/' . $this->file) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeOnModeration(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_MODERATION);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}
