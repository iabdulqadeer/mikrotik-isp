@extends('layouts.app', ['title'=>'Edit User'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit User</h1>
  </div>

  <form method="POST" action="{{ route('systemusers.update',$user) }}" class="bg-white rounded-2xl border shadow-sm p-4 space-y-4">
    @csrf @method('PUT')
    @include('system-users._form', ['submitLabel' => 'Save Changes', 'user' => $user])
  </form>
@endsection
