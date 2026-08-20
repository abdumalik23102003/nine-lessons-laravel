<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\TicketMessageRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::query()->with('user')->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('admin.tickets.index', [
            'tickets' => $query->paginate(20)->withQueryString(),
            'statuses' => Ticket::statusesList(),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        return view('admin.tickets.show', [
            'ticket' => $ticket->load('messages.user', 'user'),
        ]);
    }

    public function addMessage(TicketMessageRequest $request, Ticket $ticket): RedirectResponse
    {
        try {
            $ticket->addMessage($request->user()->id, $request->validated('message'), fromStaff: true);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.tickets.show', $ticket);
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        try {
            $ticket->close();
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.tickets.show', $ticket)->with('status', "Murojaat yopildi.");
    }
}
