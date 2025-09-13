@extends('layouts.app', ['title' => 'Plan Prices'])

@section('content')
<div class="max-w-6xl mx-auto p-4 space-y-4">
  @if (session('ok'))
    <div class="rounded-lg bg-emerald-50 text-emerald-800 px-4 py-3">{{ session('ok') }}</div>
  @endif

  <div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold">Plan Prices</h1>
    <a href="{{ route('plan-prices.create') }}" class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">New</a>
  </div>

  <div class="rounded-2xl border overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-500">
        <tr>
          <th class="px-4 py-3 text-left">Plan</th>
          <th class="px-4 py-3">Price</th>
          <th class="px-4 py-3">Interval</th>
          <th class="px-4 py-3">Stripe Price ID</th>
          <th class="px-4 py-3">Active</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
          <tr class="border-t">
            <td class="px-4 py-3 text-left">{{ $item->plan->name }}</td>
            <td class="px-4 py-3">{{ strtoupper($item->currency) }} {{ number_format($item->amount/100, 2) }}</td>
            <td class="px-4 py-3">{{ $item->interval_count }} {{ $item->interval }}{{ $item->interval_count > 1 ? 's' : '' }}</td>
            <td class="px-4 py-3">{{ $item->stripe_price_id }}</td>
            <td class="px-4 py-3">{{ $item->active ? 'Yes' : 'No' }}</td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('plan-prices.edit', $item) }}" class="px-2 py-1 rounded-lg border hover:bg-gray-50">Edit</a>
              <form action="{{ route('plan-prices.destroy', $item) }}" method="POST" class="inline-block"
                    onsubmit="return confirm('Delete this price?')">
                @csrf @method('DELETE')
                <button class="px-2 py-1 rounded-lg border hover:bg-gray-50">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div>{{ $items->links() }}</div>
</div>
@endsection
