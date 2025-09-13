@extends('layouts.app', ['title' => $e->name])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">{{ $e->name }}</h1>
    <p class="text-[12px] text-gray-500">Serial: {{ $e->serial_number ?? '—' }}</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4 space-y-3">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div><span class="text-gray-500 text-sm">Type</span><div class="font-medium">{{ $e->type }}</div></div>
      <div><span class="text-gray-500 text-sm">User</span><div class="font-medium">{{ $e->user->name ?? '—' }}</div></div>
      <div><span class="text-gray-500 text-sm">Price</span><div class="font-medium">{{ $e->currency }} {{ number_format($e->price,2) }}</div></div>
      <div><span class="text-gray-500 text-sm">Paid</span><div class="font-medium">{{ is_null($e->paid_amount) ? '—' : ($e->currency.' '.number_format($e->paid_amount,2)) }}</div></div>
      <div><span class="text-gray-500 text-sm">Outstanding</span><div class="font-medium">{{ $e->currency }} {{ number_format($e->outstanding,2) }}</div></div>
    </div>
    @if($e->notes)
      <div>
        <div class="text-gray-500 text-sm mb-1">Notes</div>
        <div class="prose max-w-none">{{ $e->notes }}</div>
      </div>
    @endif
  </div>
@endsection
