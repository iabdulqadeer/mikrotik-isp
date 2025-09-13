@extends('layouts.app', ['title' => 'Email'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">{{ $email->subject }}</h1>
    <div class="text-sm text-gray-500">To: {{ $email->to_email }} · Status: {{ ucfirst($email->status) }} · {{ optional($email->sent_at)->diffForHumans() }}</div>
  </div>
  <div class="bg-white rounded-2xl border shadow-sm p-4 prose max-w-none">
    {!! $email->message !!}
  </div>
@endsection
