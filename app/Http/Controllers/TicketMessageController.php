<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tickets.update')->only(['store', 'destroy']);
    }

    /**
     * Guard: ensure ticket belongs to logged-in owner
     */
    protected function ensureOwnership(Ticket $ticket): void
    {
        if ($ticket->owner_id !== auth()->id()) {
            abort(404);
        }
    }

    /** POST /tickets/{ticket}/messages */
    public function store(Request $request, Ticket $ticket)
    {
        $this->ensureOwnership($ticket);

        $data = $request->validate([
            'body'          => 'required|string|max:5000',
            'attachments'   => 'nullable|array|max:10',
            'attachments.*' => 'file|max:5120|mimes:jpg,jpeg,png,webp,gif,pdf,txt,log,doc,docx,xls,xlsx,zip,rar,7z',
        ]);

        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store("tickets/{$ticket->id}", 'public');
            }
        }

        TicketMessage::create([
            'owner_id'    => auth()->id(), // 🔑 stamp owner
            'ticket_id'   => $ticket->id,
            'user_id'     => $request->user()->id,
            'body'        => $data['body'],
            'attachments' => $paths ?: null,
        ]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('ok', 'Message added.');
    }

    /** DELETE /tickets/{ticket}/messages/{message} */
    public function destroy(Ticket $ticket, TicketMessage $message)
    {
        $this->ensureOwnership($ticket);

        // Ensure message belongs to both the ticket and current owner
        if ($message->ticket_id !== $ticket->id || $message->owner_id !== auth()->id()) {
            abort(404);
        }

        $message->delete();

        return back()->with('ok', 'Message removed.');
    }
}
