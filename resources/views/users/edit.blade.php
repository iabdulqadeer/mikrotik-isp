@extends('layouts.app', ['title' => 'Edit User'])

@section('content')

  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Edit User</h1>
      <p class="text-[12px] text-gray-500">Update user details and roles.</p>
    </div>
    <div class="lg:ml-auto">
      <a href="{{ route('users.show', $user) }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">View</a>
      <a href="{{ route('users.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Back</a>
    </div>
  </div>

  <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
    @csrf
    @method('PUT')
    @include('users._form')
  </form>
@endsection
