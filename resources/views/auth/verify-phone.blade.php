{{-- resources/views/auth/verify-phone.blade.php --}}
<x-guest-layout>

    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, please verify your phone number. We have sent a 6-digit code to') }}
        <b>{{ auth()->user()->phone }}</b>.
        {{ __('If you didn\'t receive the code, you can request another one.') }}
    </div>

    @if (session('status') === 'otp-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification code has been sent to your phone you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        {{-- Resend OTP --}}
        <form method="POST" action="{{ route('phone.verify.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Resend Verification Code') }}
            </x-primary-button>
        </form>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    {{-- Enter Code --}}
    <div class="mt-6">
        <form method="POST" action="{{ route('phone.verify.check') }}">
            @csrf
            <div>
                <x-input-label for="code" :value="__('Verification Code')" />
                <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required
                    autofocus placeholder="Enter 6-digit code" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-primary-button>
                    {{ __('Verify Phone') }}
                </x-primary-button>
            </div>
        </form>
    </div>

</x-guest-layout>
