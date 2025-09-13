<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailRequest;
use App\Jobs\SendEmailJob;
use App\Models\Email;
use Illuminate\Http\Request;

class EmailController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:emails.view')->only(['index','show']);
        $this->middleware('permission:emails.create')->only(['create','store']);
        $this->middleware('permission:emails.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q = trim($request->get('q'));
        $sort = $request->get('sort', 'sent_at');
        $dir  = $request->get('dir', 'desc');

        $emails = Email::query()
            ->latest('id')
            ->search($q)
            ->orderBy($sort, $dir)
            ->paginate(15)
            ->withQueryString();

        return view('emails.index', compact('emails', 'q', 'sort', 'dir'));
    }

    public function create()
    {
        return view('emails.create');
    }

    public function store(StoreEmailRequest $request)
    {
        $cc  = $request->emailsArray($request->input('cc'));
        $bcc = $request->emailsArray($request->input('bcc'));
        $tos = $request->emailsArray($request->input('to_email'));

        // Save first (queued)
        $email = Email::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'to_email'=> $request->to_email,
            'cc'      => $cc,
            'bcc'     => $bcc,
            'status'  => 'queued',
        ]);

        // Dispatch queue (you can enrich "tos" from your customers table if you want a broadcast)
        dispatch(new SendEmailJob($email, $tos ?: [$request->input('to_email')], $cc, $bcc))
            ->onQueue('mail');

        return redirect()->route('emails.index')
            ->with('status', 'Email queued to send.');
    }

    public function show(Email $email)
    {
        return view('emails.show', compact('email'));
    }

    public function destroy(Email $email)
    {
        $email->delete();
        return back()->with('status', 'Email deleted.');
    }
}
