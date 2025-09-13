{{-- Role --}}
<div>
  <label class="block text-sm font-medium mb-1">Role <span class="text-red-500">*</span></label>
  <select name="role_id" required
          class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
    <option value="">Select an option</option>
    @foreach(($roles ?? collect()) as $r)
      <option value="{{ $r->id }}"
        @selected(old('role_id', $user?->roles->first()?->id ?? null) == $r->id)>
        {{ ucfirst($r->name) }}
      </option>
    @endforeach
  </select>
</div>

{{-- First / Last --}}
<div>
  <label class="block text-sm font-medium mb-1">First name <span class="text-red-500">*</span></label>
  <input name="first_name"
         value="{{ old('first_name', $user->first_name ?? '') }}"
         class="h-10 w-full rounded-xl border border-gray-200 px-3"
         required>
</div>
<div>
  <label class="block text-sm font-medium mb-1">Last name <span class="text-red-500">*</span></label>
  <input name="last_name"
         value="{{ old('last_name', $user->last_name ?? '') }}"
         class="h-10 w-full rounded-xl border border-gray-200 px-3"
         required>
</div>

{{-- Username --}}
<div>
  <label class="block text-sm font-medium mb-1">Username <span class="text-red-500">*</span></label>
  <input name="username"
         value="{{ old('username', $user->username ?? '') }}"
         class="h-10 w-full rounded-xl border border-gray-200 px-3"
         required>
</div>

{{-- Password --}}
@if(!$user)
  <div>
    <label class="block text-sm font-medium mb-1">Password <span class="text-red-500">*</span></label>
    <input type="password"
           name="password"
           class="h-10 w-full rounded-xl border border-gray-200 px-3"
           required>
  </div>
@else
  <div>
    <label class="block text-sm font-medium mb-1">Password</label>
    <input disabled value="••••••••"
           class="h-10 w-full rounded-xl border border-gray-200 px-3">
    <p class="text-xs text-gray-500 mt-1">Use “Change Password” in actions to update.</p>
  </div>
@endif

{{-- Phone --}}
<div>
  <label class="block text-sm font-medium mb-1">Phone <span class="text-red-500">*</span></label>
  <input name="phone"
         value="{{ old('phone', $user->phone ?? '') }}"
         class="h-10 w-full rounded-xl border border-gray-200 px-3"
         required>
</div>

{{-- Email --}}
<div>
  <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
  <input type="email"
         name="email"
         value="{{ old('email', $user->email ?? '') }}"
         class="h-10 w-full rounded-xl border border-gray-200 px-3"
         required>
</div>

@if(!$user)
  <label class="inline-flex items-center gap-2 mt-2">
    <input type="checkbox" name="send_sms" value="1"
           class="rounded border-gray-300">
    <span class="text-sm">Send SMS with login credentials</span>
  </label>
@endif

<div class="pt-3">
  <button class="rounded-xl bg-orange-500 text-white px-4 py-2 hover:bg-orange-600">
    {{ $submitLabel }}
  </button>
  <a href="{{ route('systemusers.index') }}"
     class="ml-2 rounded-xl border px-4 py-2">Cancel</a>
</div>
