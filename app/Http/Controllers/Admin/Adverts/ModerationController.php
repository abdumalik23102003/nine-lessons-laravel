<?php

namespace App\Http\Controllers\Admin\Adverts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Adverts\ModerateRequest;
use App\Http\Requests\Admin\Adverts\RejectRequest;
use App\Models\Advert;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(): View
    {
        return view('admin.moderation.index', [
            'adverts' => Advert::query()
            ->onModeration()
            ->with(['user', 'category', 'region'])
            ->latest()
            ->paginate(20),
        ]);
    }

    public function approve(ModerateRequest $request, Advert $advert): RedirectResponse
    {
        try {
            $advert->moderate(Carbon::parse($request->validated('expires_at')));
        } catch (DomainException $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()
            ->route('admin.moderation.index')
            ->with('status', "E'lon Faollashtirildi.");
    }

    public function reject(RejectRequest $request, Advert $advert): RedirectResponse
    {
        try {
            $advert->reject($request->validated('reason'));
        } catch (DomainException $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()
            ->route('admin.moderation.index')
            ->with('status', "E'lon rad etildi.");
    }
}
