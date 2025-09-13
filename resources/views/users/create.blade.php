@extends('layouts.app', ['title' => 'Add User'])

@section('content')

  <div class="mb-4 flex items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Add User</h1>
      <p class="text-[12px] text-gray-500">Create a new user and assign roles.</p>
    </div>
    <div class="ml-auto">
      <a href="{{ route('users.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Back</a>
    </div>
  </div>

  <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
    @csrf
    @include('users._form', ['user' => new \App\Models\User()])
  </form>
@endsection
