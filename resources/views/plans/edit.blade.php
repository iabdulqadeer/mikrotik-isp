{{-- resources/views/plans/edit.blade.php --}}
@extends('layouts.app', ['title' => 'Edit Plan'])

@section('content')

  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit Plan</h1>
    <p class="text-[12px] text-gray-500">Update plan details.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('plans.update',$plan) }}">
      @method('PUT')
      @include('plans._form', ['submitLabel' => 'Update'])
    </form>
  </div>
@endsection
