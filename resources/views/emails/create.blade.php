@extends('layouts.app', ['title' => 'Send Email'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Send Email</h1>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4 max-w-4xl">
    <form method="POST" action="{{ route('emails.store') }}" class="space-y-4">
      @csrf
      @include('emails._form', ['submitLabel' => 'Create'])
    </form>
  </div>
@endsection
