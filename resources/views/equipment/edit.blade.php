@extends('layouts.app', ['title' => 'Edit Equipment'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit Equipment</h1>
    <p class="text-[12px] text-gray-500">Update equipment details.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('equipment.update', $e) }}">
      @method('PUT')
      @include('equipment._form', ['submitLabel' => 'Update'])
    </form>
  </div>
@endsection
