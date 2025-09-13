@extends('layouts.app', ['title' => 'Edit Device'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Edit Device: {{ $device->name }}</h1>
  </div>

  <form method="POST" action="{{ route('devices.update',$device) }}"
        class="bg-white rounded-2xl border shadow-sm p-6 grid md:grid-cols-2 gap-4">
    @csrf @method('PUT')

    <label class="text-sm">Name
      <input name="name" value="{{ old('name',$device->name) }}" class="mt-1 w-full rounded-xl border px-3 py-2" required>
      @error('name')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Host / IP
      <input name="host" value="{{ old('host',$device->host) }}" class="mt-1 w-full rounded-xl border px-3 py-2" required>
      @error('host')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Port
      <input name="port" type="number" value="{{ old('port',$device->port) }}" class="mt-1 w-full rounded-xl border px-3 py-2">
      @error('port')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Use SSL (API-SSL)
      <select name="ssl" class="mt-1 w-full rounded-xl border px-3 py-2">
        <option value="0" @selected(old('ssl',$device->ssl)==0)>No</option>
        <option value="1" @selected(old('ssl',$device->ssl)==1)>Yes</option>
      </select>
      @error('ssl')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Username
      <input name="username" value="{{ old('username',$device->username) }}" class="mt-1 w-full rounded-xl border px-3 py-2" required>
      @error('username')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">New Password (leave blank to keep)
      <input name="password" type="password" class="mt-1 w-full rounded-xl border px-3 py-2">
      @error('password')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm md:col-span-2">Identity
      <input name="identity" value="{{ old('identity',$device->identity) }}" class="mt-1 w-full rounded-xl border px-3 py-2">
      @error('identity')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <div class="md:col-span-2 flex gap-2">
      <a href="{{ route('devices.show',$device) }}" class="px-4 py-2 rounded-xl border bg-white hover:bg-gray-50">Back</a>
      <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Update</button>
    </div>
  </form>
@endsection
