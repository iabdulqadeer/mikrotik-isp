<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, TicketMessage, Customer};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tickets.list')->only('index');
        $this->middleware('permission:tickets.view')->only('show');
        $this->middleware('permission:tickets.create')->only(['create', 'store']);
        $this->middleware('permission:tickets.update')->only(['edit', 'update']);
        $this->middleware('permission:tickets.delete')->only('destroy');
    }

    /** Guardrail: ticket must belong to current owner */
    protected function ensureOwnership(Ticket $ticket): void
    {
        if ($ticket->owner_id !== auth()->id()) {
            abort(404);
        }
    }

    public function index(Request $r)
    {
        $ownerId = auth()->id();

        $filters = [
            'q'        => $r->string('q')->toString(),
            'status'   => $r->string('status')->toString(),
            'priority' => $r->string('priority')->toString(),
            'sort'     => $r->string('sort', 'created_at')->toString(),
            'dir'      => $r->string('dir', 'desc')->toString(),
        ];

        $statuses   = ['open','pending','resolved','closed'];
        $priorities = ['low','normal','high','urgent'];
        $sortables  = ['created_at','number','subject','priority','status'];

        $tickets = Ticket::with(['user','opener'])
            ->where('owner_id', $ownerId) // ← HARD OWNER SCOPE
            ->when($filters['status'], fn($q,$v) => $q->where('status',$v))
            ->when($filters['priority'], fn($q,$v) => $q->where('priority',$v))
            ->when($filters['q'], fn($q,$v) => $q->where(function($w) use ($v) {
                $w->where('number','like',"%{$v}%")
                  ->orWhere('subject','like',"%{$v}%");
            }))
            ->when(in_array($filters['sort'],$sortables, true),
                fn($q) => $q->orderBy($filters['sort'], $filters['dir']==='asc'?'asc':'desc'),
                fn($q) => $q->latest()
            )
            ->paginate(20)
            ->withQueryString();

        return view('tickets.index', compact('tickets','filters','statuses','priorities','sortables'));
    }

    public function create()
    {
        $ownerId = auth()->id();

        // List only your own sub-users to attach a ticket to
        $users = User::query()
            ->where('owner_id', $ownerId)
            ->orderBy('name')
            ->get();

        return view('tickets.create', compact('users'));
    }

    public function store(Request $r)
    {
        $ownerId = auth()->id();

        $data = $r->validate([
            'user_id'      => ['nullable','exists:users,id'],
            'subject'      => ['required','string','max:255'],
            'priority'     => ['required','in:low,normal,high,urgent'],
            'body'         => ['required','string','max:5000'],
            'attachments'  => ['nullable','array','max:10'],
            'attachments.*'=> ['file','max:5120','mimes:jpg,jpeg,png,webp,gif,pdf,txt,log,doc,docx,xls,xlsx,zip,rar,7z'],
        ]);

        // If user_id is provided, ensure it belongs to the same owner
        if (!empty($data['user_id'])) {
            $isOwned = User::where('id', $data['user_id'])
                ->where('owner_id', $ownerId)->exists();
            if (!$isOwned) {
                return back()->withErrors(['user_id' => 'Selected user is not in your account.'])->withInput();
            }
        }

        $ticket = Ticket::create([
            'owner_id'  => $ownerId,
            'number'    => 'T-'.Str::upper(Str::random(7)),
            'user_id'   => $data['user_id'] ?? null,
            'opened_by' => $r->user()->id,
            'subject'   => $data['subject'],
            'priority'  => $data['priority'],
            'status'    => 'open',
        ]);

        // save first message + attachments (JSON array of paths)
        $paths = [];
        if ($r->hasFile('attachments')) {
            foreach ($r->file('attachments') as $file) {
                $paths[] = $file->store("tickets/{$ticket->id}", 'public');
            }
        }

        TicketMessage::create([
            'owner_id'   => $ownerId,
            'ticket_id'  => $ticket->id,
            'user_id'    => $r->user()->id,
            'body'       => $data['body'],
            'attachments'=> $paths ?: null,
        ]);

        return redirect()->route('tickets.show', $ticket)->with('ok', 'Ticket opened.');
    }

    public function show(Ticket $ticket)
    {
        $this->ensureOwnership($ticket);

        $ticket->load([
            'customer',
            'messages.user' => fn($q) => $q->select('id','name','email')
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $this->ensureOwnership($ticket);

        $users = User::query()
            ->where('owner_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('tickets.edit', compact('ticket','users'));
    }

    public function update(Request $r, Ticket $ticket)
    {
        $this->ensureOwnership($ticket);

        // Posting a new message?
        if ($r->has('message_body')) {
            $r->validate(['message_body' => 'required|string|max:5000']);

            $paths = [];
            if ($r->hasFile('attachments')) {
                foreach ((array)$r->file('attachments') as $file) {
                    $paths[] = $file->store("tickets/{$ticket->id}", 'public');
                }
            }

            TicketMessage::create([
                'owner_id'   => auth()->id(),
                'ticket_id'  => $ticket->id,
                'user_id'    => $r->user()->id,
                'body'       => $r->message_body,
                'attachments'=> $paths ?: null,
            ]);

            return back()->with('ok', 'Message posted.');
        }

        // Otherwise update ticket meta
        $data = $r->validate([
            'user_id'  => ['nullable','exists:users,id'],
            'subject'  => ['required','string','max:255'],
            'priority' => ['required','in:low,normal,high,urgent'],
            'status'   => ['required','in:open,pending,resolved,closed'],
        ]);

        // Ensure any user_id selected belongs to this owner
        if (!empty($data['user_id'])) {
            $isOwned = User::where('id', $data['user_id'])
                ->where('owner_id', auth()->id())->exists();
            if (!$isOwned) {
                return back()->withErrors(['user_id' => 'Selected user is not in your account.'])->withInput();
            }
        }

        $ticket->update($data);

        return redirect()->route('tickets.show', $ticket)->with('ok', 'Ticket updated.');
    }

    public function destroy(Ticket $ticket)
    {
        $this->ensureOwnership($ticket);

        // Optionally delete stored files
        // Storage::disk('public')->deleteDirectory("tickets/{$ticket->id}");

        $ticket->delete();

        return redirect()->route('tickets.index')->with('ok', 'Ticket deleted.');
    }
}
