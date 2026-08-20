<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\TicketMessageRequest;
use App\Http\Requests\Tickets\TicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        return view('cabinet.tickets.index', [
            'tickets' => Ticket::query()
                ->forUser($request->user())
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('cabinet.tickets.create');
    }

    public function store(TicketRequest $request): RedirectResponse
    {
        $ticket = Ticket::query()->create([
            'user_id' => $request->user()->id,
            'subject' => $request->validated('subject'),
            'content' => $request->validated('content'),
            'status' => Ticket::STATUS_OPEN,
        ]);

        return redirect()->route('cabinet.tickets.show', $ticket)->with('status', "Murojaat yuborildi.");
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        return view('cabinet.tickets.show', [
            'ticket' => $ticket->load('messages.user'),
        ]);
    }

    public function addMessage(TicketMessageRequest $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('view', $ticket);

        try {
            $ticket->addMessage($request->user()->id, $request->validated('message'), fromStaff: false);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cabinet.tickets.show', $ticket);
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('view', $ticket);

        try {
            $ticket->close();
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cabinet.tickets.show', $ticket)->with('status', "Murojaat yopildi.");
    }
}
