<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Ma'lumotlarni tekshiradi va topilgan foydalanuvchini qaytaradi.
     * Sessiya bilan hech qanday ishi yo'q — faqat parolni solishtiradi.
     *
     * @throws ValidationException
     */
    public function authenticate(): User
    {
        $this->ensureIsNotRateLimited();

        $user = User::query()->where('email', $this->string('email'))->first();

        if (! $user || ! Hash::check($this->string('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => ['Login yoki parol noto\'g\'ri.'],
            ]);
        }
        if ($user->isSuspended() || $user->isDeleted()) {
            throw ValidationException::withMessages([
                'email' => ['Hisob bloklangan yoki o\'chirilgan.'],
            ]);
        }

        if ($user->isWaiting()) {
            throw ValidationException::withMessages([
                'email' => ['Hisob hali faollashtirilmagan. Emailni tasdiqlang.'],
            ]);
        }
        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => ["Juda ko'p urinish. {$seconds} soniyadan keyin qayta urinib ko'ring."],
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
