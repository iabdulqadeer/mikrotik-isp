@extends('layouts.app', ['title' => 'Create Expense'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Create Expense</h1>
    <p class="text-[12px] text-gray-500">Record a new expense entry.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
      @include('expenses._form', ['submitLabel' => 'Create'])
    </form>
  </div>
@endsection
