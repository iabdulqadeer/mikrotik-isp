{{-- resources/views/plans/show.blade.php --}}
@extends('layouts.app', ['title' => 'Plan: '.$plan->name])

@section('content')

  <div class="mb-4 flex items-center">
    <div>
      <h1 class="text-[18px] font-semibold">{{ $plan->name }}</h1>
      <p class="text-[12px] text-gray-500">Cycle: <span class="capitalize">{{ $plan->billing_cycle }}</span></p>
    </div>
    <div class="ml-auto flex gap-2">
      @can('plans.update')
        <a href="{{ route('plans.edit',$plan) }}" class="h-10 p-2 rounded-xl border bg-white hover:bg-gray-50">Edit</a>
      @endcan
      @can('plans.delete')
        <form method="POST" action="{{ route('plans.destroy',$plan) }}"
              onsubmit="return confirm('Delete plan “{{ $plan->name }}”?');">
          @csrf @method('DELETE')
          <button class="h-10 px-4 rounded-xl border bg-white text-rose-600 hover:bg-rose-50">Delete</button>
        </form>
      @endcan
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="text-[12px] text-gray-500">Speed</div>
      <div class="text-[16px] font-medium mt-1">{{ $plan->speed_label }}</div>
    </div>
    <div class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="text-[12px] text-gray-500">Price</div>
      <div class="text-[16px] font-medium mt-1">₨ {{ number_format((float)$plan->price, 2) }}</div>
    </div>
    <div class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="text-[12px] text-gray-500">Status</div>
      <div class="mt-1">
        <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $plan->active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
          {{ $plan->active ? 'Active' : 'Inactive' }}
        </span>
      </div>
    </div>
    <div class="bg-white rounded-2xl border shadow-sm p-4">
      <div class="text-[12px] text-gray-500">Created</div>
      <div class="text-[13px] mt-1 text-gray-700">{{ $plan->created_at?->toDayDateTimeString() }}</div>
    </div>
  </div>
@endsection
