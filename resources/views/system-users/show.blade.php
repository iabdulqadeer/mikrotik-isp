@extends('layouts.app', ['title'=>'User'])

@section('content')
  <div class="bg-white rounded-2xl border shadow-sm p-4">
    <div class="flex items-center justify-between mb-3">
      <div>
        <div class="text-lg font-semibold">{{ $user->name }}</div>
        <div class="text-sm text-gray-500">{{ $user->username }}</div>
      </div>
      <a href="{{ route('systemusers.edit',$user) }}" class="rounded-lg bg-indigo-600 text-white px-3 py-2">Edit</a>
    </div>

    <div class="grid md:grid-cols-2 gap-4 text-sm">
      <div><span class="text-gray-500">Email:</span> <a href="mailto:{{ $user->email }}" class="text-orange-700">{{ $user->email }}</a></div>
      <div><span class="text-gray-500">Phone:</span> <a href="tel:{{ $user->phone }}" class="text-orange-700">{{ $user->phone }}</a></div>
      <div><span class="text-gray-500">Role:</span> <span class="font-medium">{{ $user->roles->first()?->name ?? '—' }}</span></div>
      <div><span class="text-gray-500">Internet Profile:</span> {{ $user->internetProfile->name ?? '—' }}</div>
      <div><span class="text-gray-500">Last Login:</span> {{ $user->last_login_at ? $user->last_login_at->toDayDateTimeString() : 'Never' }}</div>
    </div>
  </div>
@endsection
