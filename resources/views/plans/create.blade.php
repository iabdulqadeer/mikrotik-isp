{{-- resources/views/plans/create.blade.php --}}
@extends('layouts.app', ['title' => 'Add Plan'])

@section('content')

  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Add Plan</h1>
    <p class="text-[12px] text-gray-500">Define speeds, billing cycle, and price.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('plans.store') }}">
      @include('plans._form', ['submitLabel' => 'Create'])
    </form>
  </div>
@endsection
