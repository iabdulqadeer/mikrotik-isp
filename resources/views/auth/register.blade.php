<x-guest-layout>

    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />

    
    <!-- Header / Title -->
    <div class="mb-6 text-center">

         <h2 class="text-2xl font-bold text-gray-800">Create Your Account</h2>
        <p class="text-gray-600">Start your hotspot business today</p>
        
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                {{ __('Sign in') }}
            </a>
        </p>

       
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="grid grid-cols-3 md:grid-cols-2 gap-4">
            <!-- First Name -->
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>

            <!-- Last Name -->
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- WhatsApp -->
            <div>
                <x-input-label for="whatsapp" :value="__('WhatsApp Number')" />
                <x-text-input id="whatsapp" class="block mt-1 w-full" type="text" name="whatsapp" :value="old('whatsapp')" />
                <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
            </div>
        </div>

        <!-- Customer Care Contact -->
        <div class="mt-4">
            <x-input-label for="customer_care" :value="__('Customer Care Contact')" />
            <x-text-input id="customer_care" class="block mt-1 w-full" type="text" name="customer_care" :value="old('customer_care')" />
            <x-input-error :messages="$errors->get('customer_care')" class="mt-2" />
        </div>

        <!-- Business Address -->
        <div class="mt-4">
            <x-input-label for="business_address" :value="__('Business Address')" />
            <textarea id="business_address" name="business_address" class="block mt-1 w-full rounded-md border-gray-300">{{ old('business_address') }}</textarea>
            <x-input-error :messages="$errors->get('business_address')" class="mt-2" />
        </div>

        <!-- Country -->
        <div class="mt-4">
            <x-input-label for="country" :value="__('Country')" />
            <select id="country" name="country" class="block mt-1 w-full rounded-md border-gray-300" required>
                <option value="">Select your country</option>
                <option value="UG">Uganda (UGX)</option>
                <option value="KE">Kenya (KES)</option>
                <option value="TZ">Tanzania (TZS)</option>
                <option value="RW">Rwanda (RWF)</option>
                <option value="ET">Ethiopia (ETB)</option>
                <option value="SS">South Sudan (SSP)</option>
                <option value="CD">DR Congo (CDF)</option>
                <option value="SO">Somalia (SOS)</option>
                <option value="BI">Burundi (BIF)</option>
            </select>
            <x-input-error :messages="$errors->get('country')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Terms -->
        <div class="mt-4 flex items-center">
            <input id="terms" type="checkbox" name="terms" required class="rounded border-gray-300" />
            <label for="terms" class="ml-2 text-sm">
                I agree to the <a href="#" class="text-indigo-600">Terms of Service</a> and <a href="#" class="text-indigo-600">Privacy Policy</a>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="ml-4">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
