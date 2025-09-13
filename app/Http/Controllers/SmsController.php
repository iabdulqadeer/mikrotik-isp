<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmsRequest;
use App\Jobs\SendSmsJob;
use App\Models\SmsMessage;
use App\Models\User;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sms.list')->only('index');
        $this->middleware('permission:sms.create')->only(['create','store']);
        $this->middleware('permission:sms.view')->only('show');
        $this->middleware('permission:sms.delete')->only('destroy');
    }

    public function index(Request $req)
    {
        $q = SmsMessage::with('user')
            ->when($req->search, function ($qq) use ($req) {
                $s = $req->search;
                $qq->where(function($w) use ($s){
                    $w->where('phone','like',"%$s%")
                      ->orWhere('message','like',"%$s%")
                      ->orWhereHas('user', fn($u)=>$u->where('name','like',"%$s%")
                                                     ->orWhere('email','like',"%$s%"));
                });
            })
            ->orderByDesc('id');

        $items = $q->paginate(20)->appends($req->only('search'));

        return view('sms.index', compact('items'));
    }

    public function create()
    {
        $users = User::query()->orderBy('name')->select('id','name','email','phone')->get();
        return view('sms.create', compact('users'));
    }

    public function store(StoreSmsRequest $request)
    {
        $data = $request->validated();
        $sendToAll = (bool)($data['send_to_all'] ?? false);

        $targets = $sendToAll
            ? User::whereNotNull('phone')->get()
            : collect([$data['user_id'] ? User::find($data['user_id']) : null])->filter();

        if ($targets->isEmpty()) {
            return back()->withErrors(['user_id' => __('No valid recipients found (need phone).')])->withInput();
        }

        $count = 0;
        foreach ($targets as $user) {
            if (!$user->phone) continue;

            $sms = SmsMessage::create([
                'user_id' => $user->id,
                'phone'   => $user->phone,
                'message' => $data['message'],
                'status'  => 'queued',
            ]);
            dispatch(new SendSmsJob($sms));
            $count++;
        }

        return redirect()->route('sms.index')->with('status', __("$count SMS queued."));
    }

    public function show(SmsMessage $sms)
    {
        return view('sms.show', compact('sms'));
    }

    public function destroy(SmsMessage $sms)
    {
        $sms->delete();
        return redirect()->route('sms.index')->with('status', __('SMS deleted.'));
    }
}
