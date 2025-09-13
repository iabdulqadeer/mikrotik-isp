{{-- resources/views/plans/_form.blade.php --}}
@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
  <div>
    <label class="text-[12px] text-gray-600">Name</label>
    <input name="name" value="{{ old('name', $plan->name ?? '') }}"
           class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    @error('name')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-[12px] text-gray-600">Billing Cycle</label>
    <select name="billing_cycle"
            class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      @foreach(['daily','weekly','monthly'] as $c)
        <option value="{{ $c }}" @selected(old('billing_cycle', $plan->billing_cycle ?? '') === $c)>{{ ucfirst($c) }}</option>
      @endforeach
    </select>
    @error('billing_cycle')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-[12px] text-gray-600">Download Speed (kbps)</label>
    <input type="number" min="1" name="speed_down_kbps"
           value="{{ old('speed_down_kbps', $plan->speed_down_kbps ?? '') }}"
           class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    @error('speed_down_kbps')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-[12px] text-gray-600">Upload Speed (kbps)</label>
    <input type="number" min="1" name="speed_up_kbps"
           value="{{ old('speed_up_kbps', $plan->speed_up_kbps ?? '') }}"
           class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    @error('speed_up_kbps')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-[12px] text-gray-600">Price</label>
    <input type="number" min="0" step="0.01" name="price"
           value="{{ old('price', $plan->price ?? '') }}"
           class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    @error('price')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- NEW: Stripe Price ID --}}
  <div>
    <label class="text-[12px] text-gray-600">Stripe Price ID</label>
    <input name="stripe_price_id" placeholder="price_12345..."
           value="{{ old('stripe_price_id', $plan->stripe_price_id ?? '') }}"
           class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    <div class="text-[11px] text-gray-500 mt-1">Use the <code>price_*</code> ID from Stripe.</div>
    @error('stripe_price_id')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="flex items-center gap-2 mt-6">
    <input id="active" type="checkbox" name="active" value="1"
           @checked(old('active', ($plan->active ?? true))) class="rounded border-gray-300">
    <label for="active" class="text-[13px] text-gray-700">Active</label>
  </div>
</div>

<div class="mt-4 flex items-center gap-2">
  <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
    {{ $submitLabel ?? 'Save' }}
  </button>
  <a href="{{ route('plans.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
</div>
