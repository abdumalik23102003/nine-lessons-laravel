<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Banner;
use App\Models\Dialog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $stats = [
            'adverts_count' => Advert::query()->forUser($user)->count(),
            'favorites_count' => $user->favoriteAdverts()->count(),
            'open_dialogs' => Dialog::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhere('client_id', $user->id);
                })
                ->where(function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->where('user_id', $user->id)->where('user_new_messages', '>', 0);
                    })->orWhere(function ($q2) use ($user) {
                        $q2->where('client_id', $user->id)->where('client_new_messages', '>', 0);
                    });
                })
                ->count(),
            'tickets_count' => Ticket::query()->where('user_id', $user->id)->where('status', '!=', 'closed')->count(),
            'banners_count' => Banner::query()->forUser($user)->count(),
        ];

        $moderation = null;
        if ($user->canModerate()) {
            $moderation = [
                'adverts_on_moderation' => Advert::query()->onModeration()->count(),
                'banners_on_moderation' => Banner::query()->onModeration()->count(),
                'open_tickets' => Ticket::query()->where('status', '!=', 'closed')->count(),
            ];
        }

        return view('dashboard', [
            'stats' => $stats,
            'moderation' => $moderation,
            'user' => $user,
        ]);
    }
}
