@extends('layouts.app', ['title' => 'Add Device'])

@section('content')
  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Add MikroTik Device</h1>
    <p class="text-[12px] text-gray-500">RouterOS API access will be used to connect.</p>
  </div>

  <form method="POST" action="{{ route('devices.store') }}"
        class="bg-white rounded-2xl border shadow-sm p-6 grid md:grid-cols-2 gap-4">
    @csrf

    <label class="text-sm">Name
      <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-xl border px-3 py-2" required>
      @error('name')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Host / IP
      <input name="host" value="{{ old('host') }}" class="mt-1 w-full rounded-xl border px-3 py-2" required>
      @error('host')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Port
      <input name="port" type="number" value="{{ old('port', 8728) }}" class="mt-1 w-full rounded-xl border px-3 py-2">
      @error('port')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Use SSL (API-SSL)
      <select name="ssl" class="mt-1 w-full rounded-xl border px-3 py-2">
        <option value="0" @selected(old('ssl')==='0')>No</option>
        <option value="1" @selected(old('ssl','0')==='1')>Yes</option>
      </select>
      @error('ssl')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Username
      <input name="username" value="{{ old('username') }}" class="mt-1 w-full rounded-xl border px-3 py-2" required>
      @error('username')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm">Password
      <input name="password" type="password" class="mt-1 w-full rounded-xl border px-3 py-2" required>
      @error('password')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <label class="text-sm md:col-span-2">Identity (optional)
      <input name="identity" value="{{ old('identity') }}" class="mt-1 w-full rounded-xl border px-3 py-2">
      @error('identity')<div class="text-rose-600 text-[12px] mt-1">{{ $message }}</div>@enderror
    </label>

    <div class="md:col-span-2 flex gap-2">
      <a href="{{ route('devices.index') }}" class="px-4 py-2 rounded-xl border bg-white hover:bg-gray-50">Cancel</a>
      <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Save Device</button>
    </div>
  </form>
@endsection
