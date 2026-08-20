<?php

namespace App\Http\Controllers\Admin\Banners;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banners\ModerateBannerRequest;
use App\Http\Requests\Admin\Banners\RejectBannerRequest;
use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.moderation', [
            'banners' => Banner::query()->onModeration()->with(['user', 'category'])->latest()->paginate(20),
        ]);
    }

    public function approve(ModerateBannerRequest $request, Banner $banner): RedirectResponse
    {
        try {
            $banner->moderate(Carbon::parse($request->validated('expires_at')));
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.banners.moderation.index')->with('status', "Banner faollashtirildi.");
    }

    public function reject(RejectBannerRequest $request, Banner $banner): RedirectResponse
    {
        try {
            $banner->reject($request->validated('reason'));
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.banners.moderation.index')->with('status', "Banner rad etildi.");
    }
}
