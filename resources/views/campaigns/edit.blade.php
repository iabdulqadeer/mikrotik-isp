@extends('layouts.app', ['title' => 'Edit Campaign'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit Campaign</h1>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4 max-w-4xl" x-data="{ type: @js(old('type',$campaign->type)) }">
    <form method="POST" action="{{ route('campaigns.update',$campaign) }}" enctype="multipart/form-data" class="space-y-4">
      @csrf @method('PUT')
      @include('campaigns._form', ['submitLabel' => 'Save', 'campaign'=>$campaign])
    </form>
  </div>
@endsection
