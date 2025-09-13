@extends('layouts.app', ['title' => 'User Detail'])

@section('content')

  <div class="mb-4 flex flex-col lg:flex-row lg:items-center gap-3">
    <div>
      <h1 class="text-[18px] font-semibold">{{ $user->name }}</h1>
      <p class="text-[12px] text-gray-500">User profile & roles</p>
    </div>
    <div class="lg:ml-auto flex items-center gap-2">
      @can('users.update')
        <a href="{{ route('users.edit', $user) }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Edit</a>
      @endcan
      <a href="{{ route('users.index') }}" class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Back</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <div class="p-4 md:p-6">
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <div class="text-[12px] text-gray-600">Name</div>
          <div class="text-[14px] font-medium">{{ $user->name }}</div>
        </div>
        <div>
          <div class="text-[12px] text-gray-600">Email</div>
          <div class="text-[14px] font-medium">{{ $user->email ?? '—' }}</div>
        </div>
        <div>
          <div class="text-[12px] text-gray-600">Phone</div>
          <div class="text-[14px] font-medium">{{ $user->phone ?? '—' }}</div>
        </div>
        <div>
          <div class="text-[12px] text-gray-600">Address</div>
          <div class="text-[14px] font-medium">{{ $user->address ?? '—' }}</div>
        </div>
        <div>
          <div class="text-[12px] text-gray-600">Verified</div>
          @php $isVerified = !is_null($user->email_verified_at); @endphp
          <div class="mt-1">
            <span class="px-2 py-0.5 text-[11px] rounded-full border {{ $isVerified ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-50 text-gray-600 border-gray-200' }}">
              {{ $isVerified ? 'Yes' : 'No' }}
            </span>
            @if($isVerified)
              <span class="ml-2 text-[12px] text-gray-500">{{ $user->email_verified_at->diffForHumans() }}</span>
            @endif
          </div>
        </div>
        <div>
          <div class="text-[12px] text-gray-600">Joined</div>
          <div class="text-[14px] font-medium">{{ $user->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
        </div>
        <div class="md:col-span-2">
          <div class="text-[12px] text-gray-600">Roles</div>
          <div class="mt-1">
            @forelse($user->roles as $r)
              <span class="inline-block px-2 py-0.5 text-[11px] rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200 mr-1">{{ $r->name }}</span>
            @empty
              <span class="text-[12px] text-gray-500">—</span>
            @endforelse
          </div>
        </div>
      </div>

      <div class="mt-6 flex items-center gap-2">
        @can('users.update')
          <a href="{{ route('users.edit', $user) }}" class="h-10 px-4 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">Edit</a>
        @endcan

        @can('users.delete')
          <form method="POST" action="{{ route('users.destroy', $user) }}"
                onsubmit="return confirm('Delete user “{{ $user->name }}”?');" class="ml-auto">
            @csrf @method('DELETE')
            <button class="h-10 px-4 rounded-xl border bg-white text-rose-600 hover:bg-rose-50"
                    @disabled(auth()->id()===$user->id)>
              Delete
            </button>
          </form>
        @endcan
      </div>
    </div>
  </div>
@endsection
