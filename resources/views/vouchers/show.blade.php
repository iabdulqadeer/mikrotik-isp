{{-- resources/views/vouchers/show.blade.php --}}
@extends('layouts.app', ['title' => 'Voucher: '.$voucher->code])

@section('content')

  <div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
      <div class="min-w-0">
        <h1 class="text-[18px] font-semibold">
          Voucher
          <span class="font-mono text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md align-middle">
            {{ $voucher->code }}
          </span>
        </h1>
        <p class="text-[12px] text-gray-500">View voucher details, validity, and usage metadata.</p>
      </div>

      <div class="lg:ml-auto flex items-center gap-2">
        <a href="{{ route('vouchers.index') }}"
           class="h-10 px-3 rounded-xl border bg-white hover:bg-gray-50">Back</a>

        @can('vouchers.update')
          <a href="{{ route('vouchers.edit',$voucher) }}"
             class="h-10 px-3 rounded-xl border bg-white hover:bg-gray-50">Edit</a>
        @endcan

        @can('vouchers.delete')
          <form method="POST" action="{{ route('vouchers.destroy',$voucher) }}"
                onsubmit="return confirm('Delete voucher “{{ $voucher->code }}”?');">
            @csrf @method('DELETE')
            <button class="h-10 px-3 rounded-xl border bg-white text-rose-600 hover:bg-rose-50">
              Delete
            </button>
          </form>
        @endcan
      </div>
    </div>

    {{-- Top summary strip --}}
    @php
      $badge = match($voucher->status){
        'active'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'used'    => 'bg-amber-50 text-amber-700 border-amber-200',
        'expired' => 'bg-gray-50 text-gray-600 border-gray-200',
        'revoked' => 'bg-rose-50 text-rose-600 border-rose-200',
        default   => 'bg-gray-50 text-gray-600 border-gray-200',
      };
    @endphp

    <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Status</div>
        <div class="mt-1">
          <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $badge }}">
            {{ ucfirst($voucher->status) }}
          </span>
        </div>
      </div>
      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Duration</div>
        <div class="mt-1 font-medium">{{ (int)$voucher->duration_minutes }} minutes</div>
      </div>
      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Price</div>
        <div class="mt-1 font-medium">₨ {{ number_format((float)$voucher->price, 2) }}</div>
      </div>
      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Device</div>
        <div class="mt-1 font-medium">{{ $voucher->device?->name ?? '—' }}</div>
      </div>
    </div>

    {{-- Details cards --}}
    <div class="grid gap-4 md:grid-cols-2">
      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="flex items-center justify-between">
          <h2 class="text-[14px] font-semibold">Voucher details</h2>
          <button
            type="button"
            class="text-[12px] px-2 py-1 rounded-lg border bg-white hover:bg-gray-50"
            onclick="navigator.clipboard.writeText('{{ $voucher->code }}'); this.innerText='Copied'; setTimeout(()=>this.innerText='Copy code',1300);">
            Copy code
          </button>
        </div>

        <dl class="mt-3 space-y-2 text-[14px]">
          <div class="flex items-start gap-2">
            <dt class="w-24 text-gray-500">Code</dt>
            <dd class="font-mono">{{ $voucher->code }}</dd>
          </div>
          <div class="flex items-start gap-2">
            <dt class="w-24 text-gray-500">Plan</dt>
            <dd>{{ $voucher->plan ?: '—' }}</dd>
          </div>
          <div class="flex items-start gap-2">
            <dt class="w-24 text-gray-500">Profile</dt>
            <dd>{{ $voucher->profile ?: '—' }}</dd>
          </div>
          <div class="flex items-start gap-2">
            <dt class="w-24 text-gray-500">Validity</dt>
            <dd>
              @if($voucher->valid_from || $voucher->valid_until)
                <div>
                  {{ $voucher->valid_from?->format('Y-m-d H:i') ?? '—' }}
                  <span class="text-gray-400">→</span>
                  {{ $voucher->valid_until?->format('Y-m-d H:i') ?? '—' }}
                </div>
                <div class="text-[12px] text-gray-500">
                  @if($voucher->valid_from) starts {{ $voucher->valid_from->diffForHumans() }} @endif
                  @if($voucher->valid_until) • ends {{ $voucher->valid_until->diffForHumans() }} @endif
                </div>
              @else
                —
              @endif
            </dd>
          </div>
        </dl>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <h2 class="text-[14px] font-semibold">Usage & meta</h2>
        <dl class="mt-3 space-y-2 text-[14px]">
          <div class="flex items-start gap-2">
            <dt class="w-28 text-gray-500">Created</dt>
            <dd>
              {{ $voucher->created_at?->format('Y-m-d H:i') ?? '—' }}
              <span class="text-[12px] text-gray-500">({{ $voucher->created_at?->diffForHumans() ?? '—' }})</span>
              @if($voucher->creator)
                <span class="text-gray-400">by</span> {{ $voucher->creator->name }}
              @endif
            </dd>
          </div>

          <div class="flex items-start gap-2">
            <dt class="w-28 text-gray-500">Used by</dt>
            <dd>{{ $voucher->user?->name ?? '—' }}</dd>
          </div>

          <div class="flex items-start gap-2">
            <dt class="w-28 text-gray-500">Used at</dt>
            <dd>
              {{ $voucher->used_at?->format('Y-m-d H:i') ?? '—' }}
              @if($voucher->used_at)
                <span class="text-[12px] text-gray-500">({{ $voucher->used_at->diffForHumans() }})</span>
              @endif
            </dd>
          </div>

          <div class="flex items-start gap-2">
            <dt class="w-28 text-gray-500">Notes</dt>
            <dd class="whitespace-pre-line">{{ $voucher->notes ?: '—' }}</dd>
          </div>
        </dl>
      </div>
    </div>

    {{-- Footer actions (secondary) --}}
    <div class="mt-4 flex flex-wrap gap-2">
      <a href="{{ route('vouchers.index') }}" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">Back to list</a>
      @can('vouchers.update')
        <a href="{{ route('vouchers.edit',$voucher) }}" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">Edit voucher</a>
      @endcan
    </div>

  </div>
@endsection
