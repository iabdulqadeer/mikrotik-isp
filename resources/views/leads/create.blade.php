@extends('layouts.app', ['title' => 'Create Lead'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Create Lead</h1>
    <p class="text-[12px] text-gray-500">Add a potential customer to your pipeline.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('leads.store') }}">
      @include('leads._form', ['submitLabel' => 'Create'])
    </form>
  </div>
@endsection
