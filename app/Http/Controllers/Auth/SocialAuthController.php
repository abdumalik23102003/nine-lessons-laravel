<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\NetworkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const VALID_PROVIDERS = ['google', 'facebook', 'github'];

    public function redirect(string $provider): RedirectResponse|\Illuminate\Http\Response
    {
        if (! in_array($provider, self::VALID_PROVIDERS, true)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, NetworkService $networkService): RedirectResponse
    {
        if (! in_array($provider, self::VALID_PROVIDERS, true)) {
            abort(404);
        }

        try {
            $providerUser = Socialite::driver($provider)->user();
            $result = $networkService->handleCallback($provider, $providerUser);
        } catch (\DomainException $e) {
            return redirect()->route('login')->withErrors(['email' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Ijtimoiy tarmoq orqali kirish muvaffaqiyatsiz.']);
        }

        $user = $result['user'];

        if ($user->isSuspended() || $user->isDeleted()) {
            return redirect()->route('login')->withErrors(['email' => 'Hisob bloklangan yoki o\'chirilgan.']);
        }

        if ($user->isWaiting()) {
            return redirect()->route('login')->withErrors(['email' => 'Hisob hali faollashtirilmagan. Emailni tasdiqlang.']);
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'))->with('status', 'Xush kelibsiz!');
    }
}
