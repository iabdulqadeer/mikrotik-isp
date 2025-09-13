@extends('layouts.app', ['title' => 'Edit Expense'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit Expense</h1>
    <p class="text-[12px] text-gray-500">Update this expense entry.</p>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data">
      @method('PUT')
      @include('expenses._form', ['submitLabel' => 'Update'])
    </form>
  </div>
@endsection
