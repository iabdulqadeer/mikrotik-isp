@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
  <div>
    <label class="block text-sm font-medium mb-1">Name <span class="text-rose-500">*</span></label>
    <input name="name" value="{{ old('name', $lead->name ?? '') }}" required
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
    @error('name')<div class="text-xs text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $lead->email ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Phone</label>
    <input name="phone" value="{{ old('phone', $lead->phone ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Company</label>
    <input name="company" value="{{ old('company', $lead->company ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div class="md:col-span-2">
    <label class="block text-sm font-medium mb-1">Address</label>
    <input name="address" value="{{ old('address', $lead->address ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">City</label>
    <input name="city" value="{{ old('city', $lead->city ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">State</label>
    <input name="state" value="{{ old('state', $lead->state ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Postal Code</label>
    <input name="postal_code" value="{{ old('postal_code', $lead->postal_code ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Country</label>
    <input name="country" value="{{ old('country', $lead->country ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Status</label>
    <select name="status" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3">
      @foreach(['new','contacted','qualified','won','lost'] as $s)
        <option value="{{ $s }}" @selected(old('status', $lead->status ?? 'new')===$s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Source</label>
    <input name="source" value="{{ old('source', $lead->source ?? '') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Owner (optional)</label>
    <input name="owner_id" value="{{ old('owner_id', $lead->owner_id ?? '') }}"
           placeholder="User ID" class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Last Contact</label>
    <input type="datetime-local" name="last_contact_at"
           value="{{ old('last_contact_at', isset($lead)?optional($lead->last_contact_at)->format('Y-m-d\TH:i'):'') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Next Follow Up</label>
    <input type="datetime-local" name="next_follow_up_at"
           value="{{ old('next_follow_up_at', isset($lead)?optional($lead->next_follow_up_at)->format('Y-m-d\TH:i'):'') }}"
           class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3"/>
  </div>
  <div class="md:col-span-2">
    <label class="block text-sm font-medium mb-1">Notes</label>
    <textarea name="notes" rows="5"
              class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2">{{ old('notes', $lead->notes ?? '') }}</textarea>
  </div>
</div>

<div class="mt-4 flex items-center gap-2">
  <button class="px-4 h-10 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">{{ $submitLabel ?? 'Save' }}</button>
  <a href="{{ route('leads.index') }}" class="px-4 h-10 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
</div>
