@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">

  <div>
    <label class="block text-sm font-medium mb-1">User*</label>
    <select name="user_id" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      <option value="">— Select an option —</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}" @selected(old('user_id', $e->user_id ?? '') == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
      @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">The user who has rented this equipment.</p>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Name*</label>
    <input type="text" name="name" value="{{ old('name', $e->name ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    <p class="text-xs text-gray-500 mt-1">The name of the equipment.</p>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Type*</label>
    <select name="type" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      <option value="">— Select an option —</option>
      @foreach($types as $t)
        <option value="{{ $t }}" @selected(old('type', $e->type ?? '')==$t)>{{ $t }}</option>
      @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">Choose the equipment type.</p>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Serial Number</label>
    <input type="text" name="serial_number" value="{{ old('serial_number', $e->serial_number ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
    <p class="text-xs text-gray-500 mt-1">Serial number of the equipment.</p>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Equipment Price*</label>
    <div class="flex">
      <input type="text" name="currency" value="{{ old('currency', $e->currency ?? ($defaultCurrency ?? 'USD')) }}"
             class="h-10 w-20 rounded-l-xl border border-gray-200 bg-white px-3" />
      <input type="number" step="0.01" name="price" value="{{ old('price', $e->price ?? '') }}"
             class="h-10 w-full rounded-r-xl border border-gray-200 bg-white px-3" required>
    </div>
    <p class="text-xs text-gray-500 mt-1">Price of the equipment.</p>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Paid Amount</label>
    <div class="flex">
      <input type="text" value="{{ old('currency', $e->currency ?? ($defaultCurrency ?? 'USD')) }}" disabled
             class="h-10 w-20 rounded-l-xl border border-gray-200 bg-gray-50 px-3">
      <input type="number" step="0.01" name="paid_amount" value="{{ old('paid_amount', $e->paid_amount ?? '') }}"
             class="h-10 w-full rounded-r-xl border border-gray-200 bg-white px-3">
    </div>
    <p class="text-xs text-gray-500 mt-1">Input this if the user has paid for this equipment.</p>
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm font-medium mb-1">Notes</label>
    <textarea name="notes" rows="3" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2">{{ old('notes', $e->notes ?? '') }}</textarea>
  </div>

</div>

<div class="flex items-center gap-2 mt-4">
  <button class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
    {{ $submitLabel ?? 'Save' }}
  </button>
  <a href="{{ route('equipment.index') }}" class="px-4 py-2 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
</div>
