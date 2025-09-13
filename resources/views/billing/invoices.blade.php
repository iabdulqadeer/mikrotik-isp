@extends('layouts.app', ['title' => 'Billing & Invoices'])
@section('content')

  <div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
      <div class="min-w-0">
        <h1 class="text-[18px] font-semibold">Billing & Invoices</h1>
        <p class="text-[12px] text-gray-500">See your invoice history and download PDFs for your records.</p>
      </div>

      <div class="lg:ml-auto flex items-center gap-2">
        <a href="{{ route('subscriptions.index') }}" class="h-10 px-3 rounded-xl border bg-white hover:bg-gray-50">Back</a>
        <a href="{{ route('billing.portal') }}" class="h-10 px-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Manage Billing</a>
      </div>
    </div>

    {{-- Summary cards --}}
    <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      @php
        $total = collect($invoices ?? [])->sum(fn($i) => (int)$i->total);
        $latest = collect($invoices ?? [])->first();
        $openCount = collect($invoices ?? [])->where('status','open')->count();
        $paidCount = collect($invoices ?? [])->where('status','paid')->count();
      @endphp

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Total Billed</div>
        <div class="mt-1 font-medium">
          {{ strtoupper($latest->currency ?? 'usd') }} {{ number_format(($total/100),2) }}
        </div>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Latest Invoice</div>
        <div class="mt-1 font-medium">
          {{ $latest ? (\Carbon\Carbon::parse($latest->created)->format('M d, Y')) : '—' }}
        </div>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Paid</div>
        <div class="mt-1 font-medium">{{ $paidCount }}</div>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-4">
        <div class="text-[12px] text-gray-500">Open</div>
        <div class="mt-1 font-medium">{{ $openCount }}</div>
      </div>
    </div>

    {{-- Invoices table --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <div class="px-4 py-3 border-b flex items-center justify-between">
        <h2 class="text-[14px] font-semibold">Invoice History</h2>
        <div class="text-[12px] text-gray-500">Download PDFs or manage payment methods in the billing portal.</div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
              <th class="px-4 py-3 text-left">Date</th>
              <th class="px-4 py-3 text-left">Number</th>
              <th class="px-4 py-3 text-left">Amount</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $inv)
              @php
                $badge = match($inv->status){
                  'paid'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                  'open'  => 'bg-amber-50 text-amber-700 border-amber-200',
                  'void'  => 'bg-gray-50 text-gray-600 border-gray-200',
                  'uncollectible' => 'bg-rose-50 text-rose-600 border-rose-200',
                  default => 'bg-gray-50 text-gray-600 border-gray-200',
                };
              @endphp
              <tr class="border-t">
                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($inv->created)->format('Y-m-d') }}</td>
                <td class="px-4 py-3 font-mono">{{ $inv->number }}</td>
                <td class="px-4 py-3">
                  {{ strtoupper($inv->currency) }} {{ number_format(($inv->total/100), 2) }}
                </td>
                <td class="px-4 py-3">
                  <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $badge }}">
                    {{ ucfirst($inv->status) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right">
                  <a class="px-3 py-2 rounded-lg bg-white border hover:bg-gray-50"
                     href="{{ route('billing.invoices.download', $inv->id) }}">Download PDF</a>
                </td>
              </tr>
            @empty
              <tr>
                <td class="px-4 py-6 text-gray-500" colspan="5">No invoices yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Footer actions --}}
    <div class="mt-4 flex flex-wrap gap-2">
      <a href="{{ route('subscriptions.index') }}" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">Back to subscriptions</a>
      <a href="{{ route('billing.portal') }}" class="px-3 py-2 rounded-xl bg-white border hover:bg-gray-50">Open billing portal</a>
    </div>

  </div>
@endsection
