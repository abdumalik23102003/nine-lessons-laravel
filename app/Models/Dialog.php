<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dialog extends Model
{
    use HasFactory;

    protected $table = 'advert_dialogs';
    protected $guarded = ['id'];
    protected $casts = [
        'advert_id' => 'integer',
        'user_id' => 'integer',
        'client_id' => 'integer',
        'user_new_messages' => 'integer',
        'client_new_messages' => 'integer',
    ];
    public function isOwner(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function isParticipant(int $userId): bool
    {
        return $this->user_id === $userId || $this->client_id === $userId;
    }

    public function addMessage(int $authorId, string $message): void
    {
        if (! $this->isParticipant($authorId)) {
            throw new \DomainException("Siz bu suhbat ishtirokchisi emassiz.");
        }

        $this->messages()->create([
            'user_id' => $authorId,
            'message' => $message,
        ]);

        if ($this->isOwner($authorId)) {
            $this->increment('client_new_messages');
        } else {
            $this->increment('user_new_messages');
        }
    }

    public function readBy(int $userId): void
    {
        if ($this->isOwner($userId)) {
            $this->update(['user_new_messages' => 0]);
        } elseif ($this->client_id === $userId) {
            $this->update(['client_new_messages' => 0]);
        }
    }

    public function unreadCountFor(int $userId): int
    {
        if ($this->isOwner($userId)) {
            return $this->user_new_messages;
        }
        if ($this->client_id === $userId) {
            return $this->client_new_messages;
        }

        return 0;
    }

    public function advert(): BelongsTo
    {
        return $this->belongsTo(Advert::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DialogMessage::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('client_id', $userId);
        });
    }
}
