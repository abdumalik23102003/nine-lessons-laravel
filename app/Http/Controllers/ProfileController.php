<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Notifications\PhoneVerificationCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function requestPhoneVerification(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user = $request->user();
        $user->update(['phone' => $request->input('phone')]);

        $code = $user->generatePhoneVerificationToken();
        $user->notify(new PhoneVerificationCodeNotification($code));

        return Redirect::route('profile.edit')->with('status', 'phone-code-sent');
    }

    public function verifyPhone(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();
        $verified = $user->verifyPhone($request->input('code'));

        if (! $verified) {
            return Redirect::route('profile.edit')
                ->withErrors(['code' => 'Tasdiqlash kodi noto\'g\'ri.']);
        }

        return Redirect::route('profile.edit')->with('status', 'phone-verified');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return Redirect::to('/');
    }
}
