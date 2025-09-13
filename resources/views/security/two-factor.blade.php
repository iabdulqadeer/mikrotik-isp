{{-- resources/views/security/two-factor.blade.php --}}
@extends('layouts.app', ['title' => 'Two-Factor Authentication'])

@section('content')
  <div class="max-w-3xl mx-auto p-4">
    <div class="mb-4">
      <h1 class="text-[18px] font-semibold">Two-Factor Authentication (2FA)</h1>
      <p class="text-[12px] text-gray-500">Add an extra layer of security with Google Authenticator.</p>
    </div>

    @if(!$user->two_factor_enabled)
      <div class="rounded-2xl border bg-white p-4">
        <h2 class="text-sm font-semibold mb-2">Enable 2FA</h2>
        <ol class="text-sm text-gray-600 list-decimal ml-5 space-y-1 mb-4">
          <li>Install <b>Google Authenticator</b> (or Authy/1Password) on your phone.</li>
          <li>Scan the QR code or enter the secret manually.</li>
          <li>Enter the 6-digit code to confirm.</li>
        </ol>

        @if($qr)
          <div class="flex flex-col md:flex-row gap-4 items-start">
            <img class="w-48 h-48 rounded-xl border" src="{{ $qr }}" alt="2FA QR Code">
            <div class="text-sm">
              <div class="mb-2">
                <span class="font-medium">Secret:</span>
                <code class="text-xs bg-gray-100 rounded px-2 py-1">{{ $secret }}</code>
              </div>

              <form method="POST" action="{{ route('2fa.confirm') }}" class="flex items-center gap-2">
                @csrf
                <input name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                       class="h-10 w-40 rounded-xl border border-gray-200 bg-white px-3"
                       placeholder="123456" />
                <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Confirm</button>
              </form>
              @error('code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
          </div>
        @else
          <p class="text-sm text-gray-500">Generating setup… refresh if the QR is missing.</p>
        @endif
      </div>
    @else
      <div class="rounded-2xl border bg-white p-4 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-sm font-semibold">2FA is enabled</h2>
            <p class="text-xs text-gray-500">Confirmed at {{ $user->two_factor_confirmed_at?->format('Y-m-d H:i') }}</p>
          </div>
          <form method="POST" action="{{ route('2fa.disable') }}">
            @csrf
            <button class="h-10 px-4 rounded-xl border hover:bg-gray-50">Disable</button>
          </form>
        </div>

        <div class="border-t pt-4">
          <h3 class="text-sm font-semibold mb-2">Recovery Codes</h3>
          <p class="text-xs text-gray-500 mb-3">Each code can be used once if you lose your authenticator.</p>

          @if(!empty($recovery))
            <div class="grid md:grid-cols-2 gap-2 text-xs">
              @foreach($recovery as $code)
                <div class="rounded-xl border px-3 py-2 bg-gray-50 font-mono tracking-wider">{{ $code }}</div>
              @endforeach
            </div>
          @else
            <p class="text-sm text-gray-500">No codes generated yet.</p>
          @endif

          <form method="POST" action="{{ route('2fa.recovery.regen') }}" class="mt-3">
            @csrf
            <button class="h-10 px-4 rounded-xl bg-white border hover:bg-gray-50">Regenerate Recovery Codes</button>
          </form>
        </div>
      </div>
    @endif
  </div>
@endsection
