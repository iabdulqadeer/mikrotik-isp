@php
  // expects: $user (nullable on create), $roles (collection/array of role names)
  $isEdit = isset($user) && $user?->exists;
@endphp

<div class="bg-white rounded-2xl border shadow-sm p-4 md:p-6">
  {{-- Logo / header (optional) --}}
  <div class="mb-4">
    <h2 class="text-xl font-semibold">{{ $isEdit ? 'Update account' : 'Create account' }}</h2>
    <p class="text-gray-600 text-sm">Fields aligned with public registration form.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- First Name --}}
    <div>
      <label class="block text-[12px] text-gray-600 mb-1">First Name <span class="text-rose-600">*</span></label>
      <input name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}"
             class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      @error('first_name') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Last Name --}}
    <div>
      <label class="block text-[12px] text-gray-600 mb-1">Last Name <span class="text-rose-600">*</span></label>
      <input name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}"
             class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      @error('last_name') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Email --}}
    <div class="md:col-span-2">
      <label class="block text-[12px] text-gray-600 mb-1">Email Address</label>
      <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
             class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('email') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Phone --}}
    <div>
      <label class="block text-[12px] text-gray-600 mb-1">Phone Number <span class="text-rose-600">*</span></label>
      <input name="phone" value="{{ old('phone', $user->phone ?? '') }}"
             class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      @error('phone') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- WhatsApp --}}
    <div>
      <label class="block text-[12px] text-gray-600 mb-1">WhatsApp Number</label>
      <input name="whatsapp" value="{{ old('whatsapp', $user->whatsapp ?? '') }}"
             class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('whatsapp') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Customer Care Contact --}}
    <div class="md:col-span-2">
      <label class="block text-[12px] text-gray-600 mb-1">Customer Care Contact</label>
      <input name="customer_care" value="{{ old('customer_care', $user->customer_care ?? '') }}"
             class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('customer_care') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Business Address --}}
    <div class="md:col-span-2">
      <label class="block text-[12px] text-gray-600 mb-1">Business Address</label>
      <textarea name="business_address" rows="3"
        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2">{{ old('business_address', $user->business_address ?? '') }}</textarea>
      @error('business_address') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Country --}}
    <div class="md:col-span-2">
      <label class="block text-[12px] text-gray-600 mb-1">Country <span class="text-rose-600">*</span></label>
      <select name="country" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
        @php $selected = old('country', $user->country ?? ''); @endphp
        <option value="">Select your country</option>
        <option value="UG" @selected($selected==='UG')>Uganda (UGX)</option>
        <option value="KE" @selected($selected==='KE')>Kenya (KES)</option>
        <option value="TZ" @selected($selected==='TZ')>Tanzania (TZS)</option>
        <option value="RW" @selected($selected==='RW')>Rwanda (RWF)</option>
        <option value="ET" @selected($selected==='ET')>Ethiopia (ETB)</option>
        <option value="SS" @selected($selected==='SS')>South Sudan (SSP)</option>
        <option value="CD" @selected($selected==='CD')>DR Congo (CDF)</option>
        <option value="SO" @selected($selected==='SO')>Somalia (SOS)</option>
        <option value="BI" @selected($selected==='BI')>Burundi (BIF)</option>
      </select>
      @error('country') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Password (create/optional on edit) --}}
    <div class="md:col-span-2">
      <label class="block text-[12px] text-gray-600 mb-1">
        {{ $isEdit ? 'New Password (optional)' : 'Password (optional; default "password" if empty)' }}
      </label>
      <input type="password" name="password" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('password') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
      @unless($isEdit)
        <div class="text-[11px] text-gray-500 mt-1">If left blank, a default password “password” will be set.</div>
      @endunless
    </div>

    {{-- Terms (admin panel: optional toggle to record consent flag) --}}
    <div class="md:col-span-2">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="terms" value="1" @checked(old('terms', ($user->terms ?? false) ? 1 : 0)) class="rounded border-gray-300">
        <span class="text-[13px]">Agreed to Terms of Service / Privacy Policy</span>
      </label>
      @error('terms') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Roles --}}
    <div class="md:col-span-2">
      <label class="block text-[12px] text-gray-600 mb-2">Roles</label>
      <div class="flex flex-wrap gap-2">
        @foreach($roles as $rname)
          @php
            $checked = in_array($rname, old('roles', isset($user) ? $user->roles->pluck('name')->all() : []));
          @endphp
          <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border hover:bg-gray-50">
            <input type="checkbox" name="roles[]" value="{{ $rname }}" @checked($checked)>
            <span class="text-[13px]">{{ $rname }}</span>
          </label>
        @endforeach
      </div>
      @error('roles') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
      @error('roles.*') <div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="mt-6 flex items-center gap-2">
    <button type="submit" class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
      {{ $isEdit ? 'Update User' : 'Create User' }}
    </button>
    <a href="{{ route('users.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
  </div>
</div>
