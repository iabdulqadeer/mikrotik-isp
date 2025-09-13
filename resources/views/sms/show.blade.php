@extends('layouts.app', ['title' => 'SMS Detail'])

@section('content')
  <div class="max-w-3xl mx-auto bg-white rounded-2xl border shadow-sm">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b">
      <div>
        <h1 class="text-lg font-semibold">SMS Detail</h1>
        <p class="text-xs text-gray-500">Full information about the SMS message</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('sms.index') }}"
           class="h-9 px-4 rounded-lg border bg-white hover:bg-gray-50 text-sm">Back</a>

        @can('sms.delete')
          <form method="POST" action="{{ route('sms.destroy', $sms) }}"
                onsubmit="return confirm('Delete this SMS?');">
            @csrf @method('DELETE')
            <button class="h-9 px-4 rounded-lg border text-rose-600 bg-white hover:bg-rose-50 text-sm">
              Delete
            </button>
          </form>
        @endcan
      </div>
    </div>

    {{-- Body --}}
    <div class="p-6 space-y-6 text-sm">
      {{-- User --}}
      <div class="grid grid-cols-3 gap-4">
        <dt class="font-medium text-gray-700">User</dt>
        <dd class="col-span-2">
          @if($sms->user)
            <div class="font-medium">{{ $sms->user->name }}</div>
            <div class="text-xs text-gray-500">{{ $sms->user->email }}</div>
          @else
            <span class="text-gray-400">—</span>
          @endif
        </dd>
      </div>

      {{-- Phone --}}
      <div class="grid grid-cols-3 gap-4">
        <dt class="font-medium text-gray-700">Phone</dt>
        <dd class="col-span-2 font-mono">{{ $sms->phone }}</dd>
      </div>

      {{-- Message --}}
      <div class="grid grid-cols-3 gap-4">
        <dt class="font-medium text-gray-700">Message</dt>
        <dd class="col-span-2">{{ $sms->message }}</dd>
      </div>

      {{-- Status --}}
      <div class="grid grid-cols-3 gap-4">
        <dt class="font-medium text-gray-700">Status</dt>
        <dd class="col-span-2">
          <span @class([
            'px-2 py-0.5 rounded-full text-xs font-medium border',
            'bg-gray-100 text-gray-700 border-gray-200'   => $sms->status === 'queued',
            'bg-blue-100 text-blue-700 border-blue-200'   => $sms->status === 'sent',
            'bg-green-100 text-green-700 border-green-200' => $sms->status === 'delivered',
            'bg-rose-100 text-rose-700 border-rose-200'   => $sms->status === 'failed',
          ])>
            {{ ucfirst($sms->status) }}
          </span>
        </dd>
      </div>

      {{-- Sent --}}
      <div class="grid grid-cols-3 gap-4">
        <dt class="font-medium text-gray-700">Sent At</dt>
        <dd class="col-span-2">
          {{ $sms->sent_at?->format('M d, Y H:i') ?? '—' }}
        </dd>
      </div>

      {{-- Delivered --}}
      <div class="grid grid-cols-3 gap-4">
        <dt class="font-medium text-gray-700">Delivered At</dt>
        <dd class="col-span-2">
          {{ $sms->delivered_at?->format('M d, Y H:i') ?? '—' }}
        </dd>
      </div>
    </div>
  </div>
@endsection
