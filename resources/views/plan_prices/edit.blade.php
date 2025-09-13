@extends('layouts.app', ['title' => 'Edit Plan Price'])

@section('content')
<div class="max-w-3xl mx-auto p-4 space-y-4">
  <h1 class="text-xl font-semibold">Edit Plan Price</h1>

  <form method="POST" action="{{ route('plan-prices.update', $item) }}" class="space-y-4">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm font-medium mb-1">Plan</label>
      <select name="plan_id" class="w-full rounded-lg border px-3 py-2">
        @foreach($plans as $p)
          <option value="{{ $p->id }}" @selected($p->id === $item->plan_id)>{{ $p->name }}</option>
        @endforeach
      </select>
      @error('plan_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Stripe Price ID</label>
        <input name="stripe_price_id" class="w-full rounded-lg border px-3 py-2" value="{{ old('stripe_price_id',$item->stripe_price_id) }}" required />
        @error('stripe_price_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Currency</label>
        <input name="currency" value="{{ old('currency',$item->currency) }}" class="w-full rounded-lg border px-3 py-2" />
        @error('currency') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Amount (cents)</label>
        <input type="number" name="amount" class="w-full rounded-lg border px-3 py-2" min="0" value="{{ old('amount',$item->amount) }}" required />
        @error('amount') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Interval</label>
        <select name="interval" class="w-full rounded-lg border px-3 py-2">
          @foreach(['day','week','month','year'] as $int)
            <option value="{{ $int }}" @selected($int===$item->interval)>{{ $int }}</option>
          @endforeach
        </select>
        @error('interval') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Interval Count</label>
        <input type="number" name="interval_count" value="{{ old('interval_count',$item->interval_count) }}" class="w-full rounded-lg border px-3 py-2" min="1" />
        @error('interval_count') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Role Name (optional)</label>
      <input name="role_name" class="w-full rounded-lg border px-3 py-2" value="{{ old('role_name',$item->role_name) }}" />
      @error('role_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Features (JSON)</label>
      <textarea name="features" class="w-full rounded-lg border px-3 py-2" rows="3">{{ old('features', $item->features ? json_encode($item->features) : '') }}</textarea>
      <div class="text-xs text-gray-500 mt-1">Enter a JSON array of strings.</div>
      @error('features') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="active" value="1" class="rounded" @checked($item->active)>
      <span>Active</span>
    </label>

    <div class="pt-2">
      <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Update</button>
      <a href="{{ route('plan-prices.index') }}" class="px-4 py-2 rounded-lg border hover:bg-gray-50">Cancel</a>
    </div>
  </form>
</div>
@endsection
