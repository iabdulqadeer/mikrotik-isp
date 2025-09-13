<?php

// app/Http/Controllers/Auth/RegisteredUserController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\Support\Currency;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'        => ['required','string','max:255'],
            'last_name'         => ['required','string','max:255'],
            'email'             => ['required','string','email','max:255','unique:users,email'],
            'phone'             => ['required','string','max:20'],
            'whatsapp'          => ['nullable','string','max:20'],
            'customer_care'     => ['nullable','string','max:20'],
            'business_address'  => ['required','string','max:500'],
            'country'           => ['required','string','max:5'],
            'password'          => ['required','confirmed', Rules\Password::defaults()],
            'terms'             => ['accepted'],
        ]);

        // Resolve currency (FIX)
        $cur = Currency::forCountry($request->country);

        Role::findOrCreate('user');

        $user = User::create([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'name'             => $request->first_name.' '.$request->last_name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'whatsapp'         => $request->whatsapp,
            'customer_care'    => $request->customer_care,
            'business_address' => $request->business_address,
            'country'          => $request->country,
            'currency_code'    => $cur['code'] ?? null,
            'currency_symbol'  => $cur['symbol'] ?? null,
            'password'         => Hash::make($request->password),
        ]);

        $user->assignRole('user');

        event(new Registered($user)); // sends email verification automatically
        Auth::login($user);

        // First stop: email verify notice (middleware will chain to phone as needed)
        return redirect()->route('verification.notice');
    }
}
