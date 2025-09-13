{{-- resources/views/devices/index.blade.php --}}
@extends('layouts.app', ['title' => 'MikroTik'])

@section('content')

  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">MikroTik Devices</h1>
      <p class="text-[12px] text-gray-500">Manage RouterOS endpoints you connect to via API.</p>
    </div>

    <div class="lg:ml-auto flex items-center gap-2">
      <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ request('q') }}"
               class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
               placeholder="Search name / host…">
        <select name="status" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">All</option>
          <option value="online" @selected(request('status')==='online')>Online</option>
          <option value="offline" @selected(request('status')==='offline')>Offline</option>
        </select>
        <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Filter</button>
        @if(request()->hasAny(['q','status']))
          <a href="{{ route('devices.index') }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
        @endif
      </form>

      @can('devices.create')
        <a href="{{ route('devices.create') }}"
           class="h-10 p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Add Device</a>
      @endcan
    </div>
  </div>

  @if($devices->count() === 0)
    <div class="bg-white rounded-2xl border shadow-sm p-10 grid place-items-center text-gray-500">
      <div class="text-center">
        <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
        <div class="mt-2 text-[13px]">No devices yet</div>
        @can('devices.create')
          <div class="text-[12px]">Click “Add Device” to start.</div>
        @endcan
      </div>
    </div>
  @else
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-[14px]">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Host</th>
              <th class="px-4 py-3 text-center">Port</th>
              <th class="px-4 py-3 text-center">SSL</th>
              <th class="px-4 py-3 text-left">Identity</th>
              <th class="px-4 py-3 text-left">Last Seen</th>
              @canany(['devices.test','devices.provision','devices.edit','devices.delete'])
                <th class="px-4 py-3"></th>
              @endcanany
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach($devices as $d)
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-medium">
                  @can('devices.view')
                    <a class="underline decoration-indigo-400 underline-offset-2" href="{{ route('devices.show',$d) }}">
                      {{ $d->name }}
                    </a>
                  @else
                    {{ $d->name }}
                  @endcan
                </td>
                <td class="px-4 py-3">{{ $d->host }}</td>
                <td class="px-4 py-3 text-center">{{ $d->port }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $d->ssl ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                    {{ $d->ssl ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td class="px-4 py-3">{{ $d->identity ?? '—' }}</td>
                <td class="px-4 py-3">
                  @php $online = $d->last_seen_at && $d->last_seen_at->gt(now()->subMinutes(5)); @endphp
                  <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $online ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                    {{ $online ? 'Online' : 'Offline' }}
                  </span>
                  <span class="ml-2 text-[12px] text-gray-500">
                    {{ $d->last_seen_at ? $d->last_seen_at->diffForHumans() : 'never' }}
                  </span>
                </td>

                @canany(['devices.test','devices.provision','devices.edit','devices.delete'])
                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      @can('devices.test')
                        <form method="POST" action="{{ route('devices.test', $d) }}">
                          @csrf
                          <button type="submit" class="px-3 py-2 rounded-xl border bg-white hover:bg-gray-50">
                            Test
                          </button>
                        </form>
                      @endcan

                      @can('devices.provision')
                        <a href="{{ route('devices.provision-link',$d) }}"
                           class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">
                          Provision
                        </a>
                      @endcan

                      @can('devices.edit')
                        <a href="{{ route('devices.edit',$d) }}"
                           class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">
                          Edit
                        </a>
                      @endcan

                      @can('devices.delete')
                        <form method="POST" action="{{ route('devices.destroy',$d) }}"
                              onsubmit="return confirm('Delete device “{{ $d->name }}”?');">
                          @csrf @method('DELETE')
                          <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">
                            Delete
                          </button>
                        </form>
                      @endcan
                    </div>
                  </td>
                @endcanany
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 border-t">
        {{ $devices->withQueryString()->links() }}
      </div>
    </div>
  @endif
@endsection
