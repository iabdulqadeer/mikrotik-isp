{{-- resources/views/security/two-factor-challenge.blade.php --}}
@extends('layouts.app', ['title' => 'Two-Factor Challenge'])

@section('content')

  <div class="min-h-[60vh] flex items-center">
    <div class="max-w-md mx-auto w-full p-4">
      <div class="rounded-2xl border bg-white p-6">
        <h1 class="text-[18px] font-semibold mb-1">Security Check</h1>
        <p class="text-[12px] text-gray-500 mb-4">Enter the 6-digit code from your authenticator app, or a recovery code.</p>

        <form method="POST" action="{{ route('2fa.challenge.verify') }}" class="space-y-3">
          @csrf
          <input name="code" autocomplete="one-time-code" inputmode="numeric" placeholder="123456 or RECOVERYCODE"
                 class="h-11 w-full rounded-xl border border-gray-200 bg-white px-3" />
          @error('code')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

          <button class="h-11 w-full rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Verify</button>
        </form>
      </div>
    </div>
  </div>
@endsection
