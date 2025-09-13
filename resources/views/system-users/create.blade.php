@extends('layouts.app', ['title'=>'Create User'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Create System User</h1>
    <p class="text-[12px] text-gray-500">Add a user, assign a role and (optionally) internet profile.</p>
  </div>

  <form method="POST" action="{{ route('systemusers.store') }}" class="bg-white rounded-2xl border shadow-sm p-4 space-y-4">
    @csrf
    @include('system-users._form', ['submitLabel' => 'Create', 'user' => null])
  </form>
@endsection
