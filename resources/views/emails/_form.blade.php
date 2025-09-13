@php
  $varsText = '@first_name, @last_name, @email, @phone, @package_name, @expiry_at, @account_number, @paybill, @till_number, @password, @username';
@endphp

<div>
  <label class="block text-sm font-medium mb-1">To (comma separated)</label>
  <input name="to_email" value="{{ old('to_email') }}" class="h-10 w-full rounded-xl border border-gray-200 px-3" placeholder="user@example.com, other@example.com" />
  @error('to_email')<div class="text-rose-600 text-xs mt-1">{{ $message }}</div>@enderror
</div>

<div>
  <label class="block text-sm font-medium mb-1">Subject<span class="text-rose-600">*</span></label>
  <input name="subject" value="{{ old('subject') }}" required class="h-10 w-full rounded-xl border border-gray-200 px-3" />
  @error('subject')<div class="text-rose-600 text-xs mt-1">{{ $message }}</div>@enderror
</div>

<div>
  <label class="block text-sm font-medium mb-1">Message<span class="text-rose-600">*</span></label>
  <textarea name="message" rows="8" required class="w-full rounded-xl border border-gray-200 p-3">{{ old('message') }}</textarea>
  <p class="text-xs text-gray-500 mt-1">Use the following variables to personalize your message: {{ $varsText }}</p>
  @error('message')<div class="text-rose-600 text-xs mt-1">{{ $message }}</div>@enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
  <div>
    <label class="block text-sm font-medium mb-1">CC</label>
    <input name="cc" value="{{ old('cc') }}" class="h-10 w-full rounded-xl border border-gray-200 px-3" placeholder="cc@example.com, cc2@example.com" />
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">BCC</label>
    <input name="bcc" value="{{ old('bcc') }}" class="h-10 w-full rounded-xl border border-gray-200 px-3" placeholder="bcc@example.com" />
  </div>
</div>

<div class="flex items-center gap-3 pt-2">
  <button class="inline-flex items-center gap-2 rounded-xl bg-amber-600 text-white px-4 py-2 hover:bg-amber-700">
    <span>{{ $submitLabel ?? 'Save' }}</span>
  </button>
  <a href="{{ route('emails.index') }}" class="inline-flex items-center gap-2 rounded-xl border px-4 py-2">Cancel</a>
</div>
