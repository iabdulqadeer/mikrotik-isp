@extends('layouts.app', ['title' => 'Create Equipment'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Create Equipment</h1>
    <p class="text-[12px] text-gray-500">Add a new equipment to your inventory.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('equipment.store') }}">
      @include('equipment._form', ['submitLabel' => 'Create'])
    </form>
  </div>
@endsection
