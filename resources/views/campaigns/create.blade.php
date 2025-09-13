@extends('layouts.app', ['title' => 'Create Campaign'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Create Campaigns</h1>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4 max-w-4xl" x-data="{ type: @js(old('type','banner')) }">
    <form method="POST" action="{{ route('campaigns.store') }}" enctype="multipart/form-data" class="space-y-4">
      @csrf
      @include('campaigns._form', ['submitLabel' => 'Create'])
    </form>
  </div>
@endsection
