@extends('layouts.app', ['title' => 'Users'])

@section('content')


  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">Users</h1>
      <p class="text-[12px] text-gray-500">Manage application users and their roles.</p>
    </div>

    <div class="lg:ml-auto flex items-center gap-2">
      <form method="GET" class="flex items-center gap-2">
        <input name="q" value="{{ request('q') }}"
               class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
               placeholder="Search name / email / phone…">

        <select name="role" class="h-10 rounded-xl border border-gray-200 bg-white px-3">
          <option value="">All roles</option>
          @foreach($roles as $r)
            <option value="{{ $r }}" @selected(request('role')===$r)>{{ $r }}</option>
          @endforeach
        </select>

        <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Filter</button>
        @if(request()->hasAny(['q','role']))
          <a href="{{ route('users.index') }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
        @endif
      </form>

      @can('users.create')
        <a href="{{ route('users.create') }}"
           class="h-10 p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Add User</a>
      @endcan
    </div>
  </div>

  @if($users->count() === 0)
    <div class="bg-white rounded-2xl border shadow-sm p-10 grid place-items-center text-gray-500">
      <div class="text-center">
        <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
        <div class="mt-2 text-[13px]">No users yet</div>
        <div class="text-[12px]">Click “Add User” to create one.</div>
      </div>
    </div>
  @else
     <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-[14px]">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Email</th>
              <th class="px-4 py-3 text-left">Roles</th>
              <th class="px-4 py-3 text-left">Verified</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left">Joined</th>
              <th class="px-4 py-3">Action</th>
            </tr>
          </thead>

          <tbody class="divide-y">
            @foreach($users as $u)
              @php
                $isSelf = auth()->id() === $u->id;
                $canImpersonate = auth()->user()->can('users.impersonate');
                $alreadyImpersonating = session()->has('impersonator_id');
              @endphp
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-medium">
                  <a class="underline decoration-indigo-400 underline-offset-2" href="{{ route('users.show',$u) }}">
                    {{ $u->name }}
                  </a>
                  @if(!empty($u->phone))
                    <div class="text-[12px] text-gray-500">{{ $u->phone }}</div>
                  @endif
                </td>

                <td class="px-4 py-3">{{ $u->email ?? '—' }}</td>

                <td class="px-4 py-3">
                  @php $rlist = $u->roles->pluck('name'); @endphp
                  @forelse($rlist as $r)
                    <span class="inline-block px-2 py-0.5 text-[11px] rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200 mr-1">{{ $r }}</span>
                  @empty
                    <span class="text-[12px] text-gray-500">—</span>
                  @endforelse
                </td>

                <td class="px-4 py-3">
                  @php $isVerified = !is_null($u->email_verified_at); @endphp
                  <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $isVerified ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                    {{ $isVerified ? 'Yes' : 'No' }}
                  </span>
                </td>

                {{-- NEW: Active / Deactivated --}}
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-[11px] rounded-full border
                      {{ $u->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                      {{ $u->is_active ? 'Active' : 'Deactivated' }}
                    </span>

                    @can('users.update')
                      <form method="POST" action="{{ route('users.toggle-active', $u) }}">
                        @csrf @method('PATCH')
                        <button class="px-2 py-1 text-[12px] rounded-lg border bg-white hover:bg-gray-50"
                                @disabled($isSelf)>
                          {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>

                <td class="px-4 py-3">
                  <span class="text-[12px] text-gray-500">
                    {{ $u->created_at?->diffForHumans() ?? '—' }}
                  </span>
                </td>

                <td class="px-4 py-3">
                  <div class="flex justify-end gap-2">
                    <a href="{{ route('users.show',$u) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>

                    @can('users.update')
                      <a href="{{ route('users.edit',$u) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
                    @endcan

                    {{-- NEW: Impersonate (disabled for self / inactive / if already impersonating) --}}
                    @if($canImpersonate)
                      <form method="POST" action="{{ route('users.impersonate', $u) }}">
                        @csrf
                        <button class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50"
                          @disabled($isSelf || !$u->is_active || $alreadyImpersonating)>
                          Impersonate
                        </button>
                      </form>
                    @endif

                    @can('users.delete')
                      <form method="POST" action="{{ route('users.destroy',$u) }}"
                            onsubmit="return confirm('Delete user “{{ $u->name }}”?');">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50"
                                @disabled($isSelf)>
                          Delete
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
            </tbody>

      </div>

      <div class="px-4 py-3 border-t">
        {{ $users->withQueryString()->links() }}
      </div>
    </div>
  @endif
@endsection
