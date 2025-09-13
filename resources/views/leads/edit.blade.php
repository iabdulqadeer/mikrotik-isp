@extends('layouts.app', ['title' => 'Edit Lead'])

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="text-[18px] font-semibold">Edit Lead</h1>
      <p class="text-[12px] text-gray-500">Update lead details and follow-ups.</p>
    </div>
    <form method="POST" action="{{ route('leads.destroy', $lead) }}" onsubmit="return confirm('Delete this lead?')">
      @csrf @method('DELETE')
      <button class="px-3 h-10 rounded-xl border bg-white text-rose-600 hover:bg-rose-50">Delete</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <form method="POST" action="{{ route('leads.update', $lead) }}">
      @csrf @method('PUT')
      @include('leads._form', ['submitLabel' => 'Save changes'])
    </form>
  </div>
@endsection
