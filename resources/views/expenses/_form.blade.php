@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
  <div>
  <label class="text-[12px] text-gray-600">Type <span class="text-rose-600">*</span></label>
  <select name="type"
          class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3"
          required>
      <option value="">Select an option</option>
      <option value="SMS" @selected(old('type', $expense->type ?? '')==='SMS')>SMS</option>
      <option value="Airtime" @selected(old('type', $expense->type ?? '')==='Airtime')>Airtime</option>
      <option value="Internet" @selected(old('type', $expense->type ?? '')==='Internet')>Internet</option>
      <option value="Electricity" @selected(old('type', $expense->type ?? '')==='Electricity')>Electricity</option>
      <option value="System Payment" @selected(old('type', $expense->type ?? '')==='System Payment')>System Payment</option>
      <option value="Salary" @selected(old('type', $expense->type ?? '')==='Salary')>Salary</option>
      <option value="Other" @selected(old('type', $expense->type ?? 'Other')==='Other')>Other</option>
  </select>
  @error('type')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
</div>


  <div>
    <label class="text-[12px] text-gray-600">Amount <span class="text-rose-600">*</span></label>
    <input type="number" step="0.01" min="0" name="amount" placeholder="0.00"
           value="{{ old('amount', $expense->amount ?? '') }}"
           class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    @error('amount')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-[12px] text-gray-600">Date <span class="text-rose-600">*</span></label>
    <input type="datetime-local" name="spent_at"
           value="{{ old('spent_at', isset($expense->spent_at) ? $expense->spent_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
           class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
    @error('spent_at')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="text-[12px] text-gray-600">Payment method <span class="text-rose-600">*</span></label>
    <select name="payment_method" class="mt-1 h-10 w-full rounded-xl border border-gray-200 bg-white px-3" required>
      @foreach(['Cash','Bank Transfer','Mobile Money','Card','Other'] as $m)
        <option value="{{ $m }}" @selected(old('payment_method', $expense->payment_method ?? 'Other')===$m)>{{ $m }}</option>
      @endforeach
    </select>
    @error('payment_method')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>

 {{-- Drag & Drop Receipt (single file) --}}
<div class="md:col-span-2"
     x-data="{
        file: null,
        name: '',
        isOver: false,
        preview: null,
        clear() {
          this.file = null; this.name = ''; this.preview = null;
          const input = $refs.input; input.value = '';
        },
        setFile(f) {
          if (!f) return;
          this.file = f; this.name = f.name;
          // image preview if image/*
          if (f.type.startsWith('image/')) {
            const r = new FileReader();
            r.onload = e => this.preview = e.target.result;
            r.readAsDataURL(f);
          } else {
            this.preview = null;
          }
        }
     }"
     x-on:dragover.prevent="isOver=true"
     x-on:dragleave.prevent="isOver=false"
     x-on:drop.prevent="
        isOver=false;
        const f = $event.dataTransfer.files[0];
        if(f){ $refs.input.files = $event.dataTransfer.files; setFile(f); }
     ">
  <label class="text-[12px] text-gray-600">Receipt</label>

  {{-- Hidden native input (for form submit) --}}
  <input
      x-ref="input"
      type="file"
      name="receipt"
      accept=".jpg,.jpeg,.png,.pdf"
      class="sr-only"
      x-on:change="setFile($event.target.files[0])"
  />

  {{-- Drop zone --}}
  <div
      class="mt-1 rounded-2xl border border-dashed px-4 py-6 bg-white text-center cursor-pointer transition
             hover:border-indigo-300 hover:bg-indigo-50/40
             flex flex-col items-center justify-center gap-2"
      :class="isOver ? 'border-indigo-400 bg-indigo-50/60' : 'border-gray-300'"
      x-on:click="$refs.input.click()"
      x-on:keyup.enter="$refs.input.click()"
      tabindex="0"
      role="button"
      aria-label="Upload receipt by browsing or dropping a file"
  >
    {{-- Icon --}}
    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M7 16a4 4 0 1 1 6.906-3.107L14 13m0 0 2-2m-2 2-2-2m8 7H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h5l2 2h5a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2z"/>
    </svg>

    <div class="text-[13px] text-gray-700">
      <span class="font-medium">Drag & drop</span> your receipt here, or
      <span class="underline text-indigo-600">browse</span>
    </div>
    <div class="text-[12px] text-gray-500">
      JPG, PNG, or PDF up to 5&nbsp;MB
    </div>

    {{-- Selected file name / preview --}}
    <template x-if="name">
      <div class="mt-3 w-full max-w-md">
        <div class="flex items-center justify-between gap-3 rounded-xl border bg-white px-3 py-2 text-left">
          <div class="truncate text-[13px]">
            <span class="text-gray-500">Selected:</span>
            <span class="font-medium" x-text="name"></span>
          </div>
          <button type="button" class="text-[12px] px-2 py-1 rounded-lg border hover:bg-gray-50" x-on:click="clear()">
            Remove
          </button>
        </div>

        {{-- Image preview (if image) --}}
        <template x-if="preview">
          <div class="mt-2">
            <img :src="preview" alt="Receipt preview"
                 class="max-h-48 rounded-lg border object-contain mx-auto">
          </div>
        </template>
      </div>
    </template>
  </div>

  {{-- Existing file (from DB) --}}
  @if(!empty($expense->receipt_url))
    <div class="text-[12px] mt-2">
      Current: <a class="underline text-indigo-600" target="_blank" href="{{ $expense->receipt_url }}">View</a>
    </div>
  @endif

  @error('receipt')<div class="text-[12px] text-rose-600 mt-2">{{ $message }}</div>@enderror
</div>


  <div class="md:col-span-2">
    <label class="text-[12px] text-gray-600">Description</label>
    <textarea name="description" rows="3"
              class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2"
              placeholder="Optional note…">{{ old('description', $expense->description ?? '') }}</textarea>
    @error('description')<div class="text-[12px] text-rose-600 mt-1">{{ $message }}</div>@enderror
  </div>
</div>

<div class="mt-4 flex items-center gap-2">
  <button class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
    {{ $submitLabel ?? 'Save' }}
  </button>
  <a href="{{ route('expenses.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
</div>
