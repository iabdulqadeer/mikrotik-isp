{{-- resources/views/vouchers/_form.blade.php --}}
@php
  /** @var \App\Models\Voucher|null $voucher */
  $isEdit = isset($voucher) && $voucher;

  // helpers for datetime-local
  $fmt = fn($dt) => $dt ? $dt->format('Y-m-d\TH:i') : '';

  // values with old() fallback
  $val = fn ($key, $default = null) =>
      old($key, $isEdit ? ($voucher->{$key} ?? $default) : $default);
@endphp

@csrf

<div class="space-y-4">
  {{-- Device --}}
  <div>
    <label class="text-[12px] text-gray-600">Device (optional)</label>
    <select name="device_id" class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      <option value="">— None —</option>
      @foreach($devices as $d)
        <option value="{{ $d->id }}"
          @selected((string)$val('device_id') === (string)$d->id)>{{ $d->name }}</option>
      @endforeach
    </select>
    @error('device_id')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- Plan / Profile --}}
  <div class="grid md:grid-cols-2 gap-3">
    <div>
      <label class="text-[12px] text-gray-600">Plan</label>
      <input name="plan" value="{{ $val('plan') }}"
             class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3"
             placeholder="Internet Day" required>
      @error('plan')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="text-[12px] text-gray-600">Profile</label>
      <input name="profile" value="{{ $val('profile') }}"
             class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3"
             placeholder="hotspot-1h" required>
      @error('profile')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- Duration / Price (+ Status on edit) --}}
  <div class="grid md:grid-cols-{{ $isEdit ? '3' : '2' }} gap-3">
    <div>
      <label class="text-[12px] text-gray-600">Duration (minutes)</label>
      <input type="number" name="duration_minutes" value="{{ $val('duration_minutes', 60) }}"
             class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('duration_minutes')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="text-[12px] text-gray-600">Price</label>
      <input type="number" step="0.01" name="price" value="{{ $val('price', 0) }}"
             class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('price')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
    </div>

    @if($isEdit)
      <div>
        <label class="text-[12px] text-gray-600">Status</label>
        @php $status = $val('status', 'active'); @endphp
        <select name="status" class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
          @foreach(['active','used','expired','revoked'] as $s)
            <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
        @error('status')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
      </div>
    @endif
  </div>

  {{-- Batch-only fields (create mode only) --}}
  @unless($isEdit)
    <div class="grid md:grid-cols-3 gap-3">
      <div>
        <label class="text-[12px] text-gray-600">Count (batch)</label>
        <input type="number" name="count" value="{{ $val('count', 1) }}"
               class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
        @error('count')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="text-[12px] text-gray-600">Code Prefix (optional)</label>
        <input name="code_prefix" value="{{ $val('code_prefix') }}"
               class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" placeholder="EAWC">
        @error('code_prefix')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="text-[12px] text-gray-600">Code Length</label>
        <input type="number" name="code_length" value="{{ $val('code_length', 10) }}"
               class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
        @error('code_length')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
      </div>
    </div>
  @endunless

  {{-- Validity --}}
  <div class="grid md:grid-cols-2 gap-3">
    <div>
      <label class="text-[12px] text-gray-600">Valid From</label>
      <input type="datetime-local" name="valid_from"
             value="{{ old('valid_from', $isEdit ? $fmt($voucher->valid_from) : null) }}"
             class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('valid_from')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="text-[12px] text-gray-600">Valid Until</label>
      <input type="datetime-local" name="valid_until"
             value="{{ old('valid_until', $isEdit ? $fmt($voucher->valid_until) : null) }}"
             class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @error('valid_until')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- Notes --}}
  <div>
    <label class="text-[12px] text-gray-600">Notes</label>
    <textarea name="notes" rows="3"
              class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3">{{ $val('notes') }}</textarea>
    @error('notes')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>
</div>

{{-- Footer buttons (same look as create) --}}
<div class="mt-4 flex items-center gap-2">
  <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
    {{ $submitLabel ?? ($isEdit ? 'Save changes' : 'Save') }}
  </button>
  <a href="{{ route('vouchers.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
</div>
