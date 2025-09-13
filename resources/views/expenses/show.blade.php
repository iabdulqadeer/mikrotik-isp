@extends('layouts.app', ['title' => 'Expense Detail'])

@section('content')
  {{-- Header --}}
  <div class="mb-4 flex items-center gap-2">
    <div>
      <h1 class="text-[18px] font-semibold">Expense</h1>
      <p class="text-[12px] text-gray-500">Detailed view</p>
    </div>

    <div class="ml-auto flex items-center gap-2">
      <a href="{{ route('expenses.index') }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Back</a>
      <a href="{{ route('expenses.edit',$expense) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
      <form method="POST" action="{{ route('expenses.destroy',$expense) }}"
            onsubmit="return confirm('Delete this expense?');">
        @csrf @method('DELETE')
        <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">Delete</button>
      </form>
    </div>
  </div>

  {{-- Main Card --}}
  <div class="bg-white rounded-2xl border shadow-sm p-4">
    {{-- Top row: Type + Amount + Date + Method --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="rounded-xl border bg-white p-3">
        <div class="text-[11px] text-gray-500">Type</div>
        <div class="mt-0.5 font-medium">{{ $expense->type }}</div>
      </div>

      <div class="rounded-xl border bg-white p-3">
        <div class="text-[11px] text-gray-500">Amount</div>
        <div class="mt-0.5 font-semibold text-[18px]">
          {{ number_format($expense->amount, 2) }}
        </div>
      </div>

      <div class="rounded-xl border bg-white p-3">
        <div class="text-[11px] text-gray-500">Date</div>
        <div class="mt-0.5 font-medium">
          {{ $expense->spent_at?->format('M d, Y H:i') }}
        </div>
      </div>

      <div class="rounded-xl border bg-white p-3">
        <div class="text-[11px] text-gray-500">Payment Method</div>
        <div class="mt-0.5">
          <span class="px-2 py-0.5 text-[11px] rounded-full border bg-gray-50 text-gray-700">
            {{ $expense->payment_method }}
          </span>
        </div>
      </div>
    </div>

    {{-- Description --}}
    <div class="mt-4">
      <div class="text-[12px] text-gray-500">Description</div>
      <div class="mt-1 text-[14px]">{{ $expense->description ?: '—' }}</div>
    </div>

    {{-- Receipt Preview / Link --}}
    <div class="mt-4">
      <div class="flex items-center justify-between">
        <div class="text-[12px] text-gray-500">Receipt</div>
        @if($expense->receipt_url)
          <a class="text-[12px] underline text-indigo-600" target="_blank" href="{{ $expense->receipt_url }}">
            Open in new tab
          </a>
        @endif
      </div>

      @php
        $url = $expense->receipt_url;
        $ext = $url ? strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)) : null;
      @endphp

      @if($url)
        {{-- Image preview --}}
        @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
          <div class="mt-2">
            <img src="{{ $url }}" alt="Receipt" class="max-h-96 rounded-xl border object-contain">
          </div>
        {{-- PDF inline --}}
        @elseif($ext === 'pdf')
          <div class="mt-2 rounded-xl border overflow-hidden">
            <object data="{{ $url }}" type="application/pdf" class="w-full h-[600px]">
              <div class="p-4 text-[13px]">
                Your browser cannot display PDFs inline.
                <a class="underline text-indigo-600" target="_blank" href="{{ $url }}">Download PDF</a>
              </div>
            </object>
          </div>
        {{-- Fallback: just a link --}}
        @else
          <div class="mt-2 text-[13px]">
            <a class="underline text-indigo-600" target="_blank" href="{{ $url }}">View receipt</a>
          </div>
        @endif
      @else
        <div class="mt-1 text-gray-500 text-[14px]">—</div>
      @endif
    </div>

    {{-- Meta --}}
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-[12px] text-gray-500">
      <div>Created: <span class="font-medium text-gray-700">{{ $expense->created_at?->format('M d, Y H:i') }}</span></div>
      <div>Updated: <span class="font-medium text-gray-700">{{ $expense->updated_at?->format('M d, Y H:i') }}</span></div>
    </div>
  </div>
@endsection
