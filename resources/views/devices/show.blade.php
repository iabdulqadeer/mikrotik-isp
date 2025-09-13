@extends('layouts.app', ['title' => $device->name])

@section('content')

  <div class="grid lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
      <div class="bg-white rounded-2xl border shadow-sm p-6">
        <div class="flex items-start justify-between">
          <div>
            <h1 class="text-[18px] font-semibold">{{ $device->name }}</h1>
            <div class="text-[12px] text-gray-500">Host: {{ $device->host }}:{{ $device->port }} • SSL: {{ $device->ssl ? 'Yes' : 'No' }}</div>
          </div>
          <div class="flex gap-2">
            {{-- TEST (POST) --}}
            <form method="POST" action="{{ route('devices.test', $device) }}">
              @csrf
              <button type="submit" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">
                Test
              </button>
            </form>

            {{-- PROVISION (GET link) --}}
            <a class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50"
               href="{{ route('devices.provision-link', $device) }}">
              Provision
            </a>

            {{-- EDIT (GET link) --}}
            <a class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50"
               href="{{ route('devices.edit', $device) }}">
              Edit
            </a>
          </div>

        </div>

        <div class="mt-4 grid sm:grid-cols-2 gap-3">
          <div class="p-3 rounded-xl bg-gray-50">
            <div class="text-[12px] text-gray-500">Identity</div>
            <div class="font-medium">{{ $device->identity ?? '—' }}</div>
          </div>
          <div class="p-3 rounded-xl bg-gray-50">
            <div class="text-[12px] text-gray-500">Last seen</div>
            <div class="font-medium">{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'never' }}</div>
          </div>
        </div>
      </div>

      {{-- Optional: Recent logs (fill from controller if you have) --}}
      <div class="bg-white rounded-2xl border shadow-sm p-6">
        <div class="font-medium mb-3">Recent Activity</div>
        <div class="text-[13px] text-gray-500">Hook this to your logs/audits if available.</div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="bg-white rounded-2xl border shadow-sm p-6">
        <div class="font-medium mb-3">Quick Actions</div>
        <form method="POST" action="{{ route('devices.test', $device) }}">
          @csrf
          <button class="w-full mb-2 px-4 py-2 rounded-xl border bg-white hover:bg-gray-50">
            Test Connection
          </button>
        </form>

        <a href="{{ route('devices.provision-link',$device) }}" class="block w-full mb-2 px-4 py-2 text-center rounded-xl border bg-white hover:bg-gray-50">Provision Script</a>
        <form method="POST" action="{{ route('devices.destroy',$device) }}" onsubmit="return confirm('Delete this device?');">
          @csrf @method('DELETE')
          <button class="w-full px-4 py-2 rounded-xl border bg-white text-rose-600 hover:bg-rose-50">Delete</button>
        </form>
      </div>
    </div>
  </div>
@endsection
