<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_ANSWERED = 'answered';
    public const STATUS_CLOSED = 'closed';

    protected $guarded = ['id'];

    public static function statusesList(): array
    {
        return [
            self::STATUS_OPEN => 'Ochiq',
            self::STATUS_ANSWERED => 'Javob berilgan',
            self::STATUS_CLOSED => 'Yopilgan',
        ];
    }

    public function addMessage(int $userId, string $message, bool $fromStaff): void
    {
        if (! $this->allowsMessages()) {
            throw new \DomainException("Ticket yopilgan, xabar qo'shib bo'lmaydi.");
        }

        $this->messages()->create([
            'user_id' => $userId,
            'message' => $message,
        ]);

        $this->update(['status' => $fromStaff ? self::STATUS_ANSWERED : self::STATUS_OPEN]);
    }

    public function close(): void
    {
        if ($this->isClosed()) {
            throw new \DomainException('Ticket allaqachon yopilgan.');
        }

        $this->update(['status' => self::STATUS_CLOSED]);
    }

    public function reopen(): void
    {
        if (! $this->isClosed()) {
            throw new \DomainException('Ticket yopilmagan.');
        }

        $this->update(['status' => self::STATUS_OPEN]);
    }

    public function allowsMessages(): bool
    {
        return ! $this->isClosed();
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}
