{{-- resources/views/system-users/index.blade.php --}}
@extends('layouts.app', ['title' => 'System Users'])

@section('content')
  {{-- header --}}
<div class="mb-4 flex items-center justify-between">
  <h1 class="text-[18px] font-semibold">System Users</h1>

  <div class="flex items-center gap-2">
    @can('roles.edit')
      <a href="{{ route('systemusers.roles.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 bg-white hover:bg-gray-50">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v-2m0 0a2 2 0 1 0-4 0v2a2 2 0 0 0 4 0zm0 0h6M6 12h12M6 18h12"/>
        </svg>
        Manage Roles
      </a>
    @endcan

    @can('users.create')
      <a href="{{ route('systemusers.create') }}"
         class="inline-flex items-center gap-2 rounded-xl bg-orange-500 text-white px-3 py-2 hover:bg-orange-600">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Create User
      </a>
    @endcan
  </div>
</div>


 <div class="bg-white rounded-2xl border shadow-sm">

 {{-- Sticky tabs --}}
<div class="sticky top-[56px] lg:top-[56px] z-30 bg-white/85 backdrop-blur rounded-t-2xl border-b">
  <div class="px-3 py-2">
    <div class="flex gap-1 overflow-x-auto no-scrollbar">
      @foreach($tabs as $key => $meta)
        <a href="{{ route('systemusers.index', ['tab' => $key, 'q' => $q]) }}"
           class="group inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm border
                  {{ $active === $key
                      ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
                      : 'hover:bg-gray-50 border-transparent text-gray-700' }}">
          <svg class="w-4 h-4 {{ $active === $key ? 'text-indigo-700' : 'text-gray-500 group-hover:text-gray-700' }}"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="{{ $meta['icon'] }}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
          </svg>
          {{ $meta['label'] }}
          @if($active === $key)
            <span class="ml-1 inline-block h-1 w-1 rounded-full bg-indigo-600"></span>
          @endif
        </a>
      @endforeach
    </div>
  </div>
</div>

    <div class="p-4 flex items-center justify-between gap-3">
      <form method="get" class="flex-1">
        <input type="hidden" name="tab" value="{{ $active }}">
        <div class="relative">
          <input name="q" value="{{ $q }}" placeholder="Search"
                 class="w-full h-10 rounded-xl border border-gray-200 pl-9 pr-3"/>
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400"
               fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/>
          </svg>
        </div>
      </form>
    </div>


    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-600">
          <tr class="border-t">
            <th class="text-left font-medium py-3 px-4">Name</th>
            <th class="text-left font-medium py-3 px-4">Phone</th>
            <th class="text-left font-medium py-3 px-4">Email</th>
            <th class="text-left font-medium py-3 px-4">Role</th>
            <th class="text-left font-medium py-3 px-4">Last Login</th>
            <th class="text-right font-medium py-3 px-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr class="border-t">
              <td class="py-3 px-4">
                <div class="font-medium">{{ $u->name ?? $u->username }}</div>
                <div class="text-xs text-gray-500">{{ $u->username }}</div>
              </td>
              <td class="py-3 px-4">
                <a href="tel:{{ $u->phone }}" class="text-orange-700 font-medium">{{ $u->phone }}</a>
              </td>
              <td class="py-3 px-4">
                <a href="mailto:{{ $u->email }}" class="text-orange-700 font-medium">{{ $u->email }}</a>
              </td>
              <td class="py-3 px-4">
                @php $role = $u->roles->first()?->name ?? '—'; @endphp
                <span class="inline-flex items-center rounded-full bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 text-xs">{{ strtoupper($role) }}</span>
              </td>
              <td class="py-3 px-4">
                @if($u->last_login_at) <span>{{ $u->last_login_at->diffForHumans() }}</span>
                @else <span class="text-gray-500">Never</span> @endif
              </td>

              {{-- Actions --}}
              <td class="py-3 px-4 text-right">
                <div class="relative inline-block" data-menu>
                    <button type="button"
                            class="inline-flex items-center rounded-xl border px-3 py-1.5 hover:bg-gray-50"
                            data-menu-button>
                      <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                      </svg>
                      Actions
                    </button>

                    <div class="hidden absolute right-0 mt-1 w-56 bg-white border rounded-xl shadow-lg z-10"
                         data-menu-popover>
                         
                      @can('users.view')
                        <a class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50"
                           href="{{ route('systemusers.show',$u) }}">
                          <span class="text-gray-700">View user</span>
                        </a>
                      @endcan

                      @can('users.edit')
                        <a class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50"
                           href="{{ route('systemusers.edit',$u) }}">
                          <span class="text-gray-700">Edit user</span>
                        </a>
                      @endcan

                      @can('users.edit')
                        {{-- Change Password → open modal --}}
                        <button type="button"
                                class="w-full text-left px-3 py-2 hover:bg-gray-50 border-t"
                                data-open="#pwModal-{{ $u->id }}">
                          Change Password
                        </button>
                      @endcan

                      @can('users.token') {{-- or just reuse users.edit if you prefer --}}
                        <button type="button"
                                class="w-full text-left px-3 py-2 hover:bg-gray-50 border-t"
                                data-open="#tokenModal-{{ $u->id }}">
                          Generate API Token
                        </button>
                      @endcan

                    </div>
                  </div>



  {{-- Change Password Modal (labels on left + visible footer) --}}
<dialog id="pwModal-{{ $u->id }}" class="rounded-2xl p-0 w-full max-w-2xl">
  <form method="post" action="{{ route('systemusers.password', $u) }}"
        class="bg-white rounded-2xl border shadow-sm p-6">
    @csrf

    {{-- Header --}}
    <div class="flex flex-col items-center text-center space-y-2 mb-6">
      <div class="flex items-center justify-center h-12 w-12 rounded-full bg-amber-100">
        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M12 5a7 7 0 110 14a7 7 0 010-14z"/>
        </svg>
      </div>
      <h2 class="text-xl font-semibold">Change Password</h2>
      <p class="text-sm text-gray-500">
        Set a new password for this user. The user will be required to use this password on next login.
      </p>
    </div>

    {{-- Form rows: label left / input right --}}
    <div class="space-y-4">
      {{-- New password --}}
      <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-start">
        <label class="md:col-span-2 text-sm font-medium text-gray-700 md:text-right md:pt-2">
          New Password <span class="text-red-500">*</span>
        </label>
        <div class="md:col-span-3">
          <div class="relative">
            <input type="password" name="password" required
                   class="h-11 w-full rounded-xl border border-gray-200 px-3 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="button" onclick="togglePw(this)"
                    class="absolute inset-y-0 right-0 px-3 flex items-center">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0a3 3 0 016 0m7 0c0 3-4.5 7-10 7S2 15 2 12s4.5-7 10-7s10 4 10 7z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      {{-- Confirm password --}}
      <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-start">
        <label class="md:col-span-2 text-sm font-medium text-gray-700 md:text-right md:pt-2">
          Confirm Password <span class="text-red-500">*</span>
        </label>
        <div class="md:col-span-3">
          <div class="relative">
            <input type="password" name="password_confirmation" required
                   class="h-11 w-full rounded-xl border border-gray-200 px-3 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="button" onclick="togglePw(this)"
                    class="absolute inset-y-0 right-0 px-3 flex items-center">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0a3 3 0 016 0m7 0c0 3-4.5 7-10 7S2 15 2 12s4.5-7 10-7s10 4 10 7z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Footer buttons --}}
    <div class="mt-6 flex justify-end gap-2">
      <button type="button" class="rounded-xl border px-4 py-2" data-close>Cancel</button>
      <button type="submit"
              class="rounded-xl bg-orange-500 text-white px-5 py-2.5 hover:bg-orange-600">
        Confirm
      </button>
    </div>
  </form>
</dialog>



{{-- Generate API Token Modal --}}
@php
  $suggest = \Illuminate\Support\Str::slug(($u->username ?: \Illuminate\Support\Str::before($u->email, '@')).'-api-token');
@endphp

<dialog id="tokenModal-{{ $u->id }}" class="rounded-2xl p-0 w-full max-w-2xl">
  <form method="post" action="{{ route('systemusers.token', $u) }}"
        class="bg-white rounded-2xl border shadow-sm p-6 space-y-6">
    @csrf

    {{-- Header --}}
    <div class="flex flex-col items-center text-center space-y-2">
      <div class="flex items-center justify-center h-12 w-12 rounded-full bg-amber-100">
        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M12 5a7 7 0 110 14a7 7 0 010-14z"/>
        </svg>
      </div>
      <h2 class="text-xl font-semibold">Generate API Token</h2>
      <p class="text-sm text-gray-500 max-w-md">
        Generate an API token for this user. Clicking confirm below will create a new token.
      </p>
    </div>

    {{-- Token name field (label left / input right like your style) --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-start">
      <label class="md:col-span-2 text-sm font-medium text-gray-700 md:text-right md:pt-2">
        Token Name <span class="text-red-500">*</span>
      </label>
      <div class="md:col-span-3">
        <input type="text" name="token_name" required
               value="{{ $suggest }}"
               class="h-11 w-full rounded-xl border border-gray-200 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>
    </div>

    {{-- Footer --}}
    <div class="flex justify-end gap-2">
      <button type="button" class="rounded-xl border px-4 py-2" data-close>Cancel</button>
      <button type="submit" class="rounded-xl bg-orange-500 text-white px-5 py-2.5 hover:bg-orange-600">
        Confirm
      </button>
    </div>
  </form>
</dialog>



                
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="py-8 text-center text-gray-500">No users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">{{ $users->links() }}</div>
  </div>

  {{-- Token reveal --}}
  @if(session('token_plain'))
    <div class="mt-4 rounded-xl border bg-amber-50 px-4 py-3">
      <div class="font-medium text-amber-900 mb-1">API Token (copy now – shown only once)</div>
      <div class="flex items-center gap-2">
        <input id="tokenPlain" class="flex-1 h-10 rounded-lg border border-amber-300 bg-white px-3" readonly value="{{ session('token_plain') }}">
        <button onclick="navigator.clipboard.writeText(document.getElementById('tokenPlain').value)" class="rounded-lg bg-orange-500 text-white px-3 py-2">Copy</button>
      </div>
    </div>
  @endif

  {{-- Actions dropdown + modal JS (vanilla, no deps) --}}
  <script>
    // dropdowns
    document.addEventListener('click', (e) => {
      // close all
      document.querySelectorAll('[data-menu-popover]').forEach(p => p.classList.add('hidden'));
      // open target?
      const btn = e.target.closest('[data-menu-button]');
      if (btn) {
        const root = btn.closest('[data-menu]');
        root.querySelector('[data-menu-popover]').classList.toggle('hidden');
        e.stopPropagation();
      }
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('[data-menu-popover]').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('dialog[open]').forEach(d => d.close());
      }
    });

    // open modal
    document.querySelectorAll('[data-open]').forEach(btn => {
      btn.addEventListener('click', () => {
        const dlg = document.querySelector(btn.getAttribute('data-open'));
        dlg?.showModal();
      });
    });
    // close modal
    document.querySelectorAll('dialog [data-close]').forEach(btn => {
      btn.addEventListener('click', () => btn.closest('dialog')?.close());
    });
  </script>

<script>
  function togglePw(btn) {
    const input = btn.parentElement.querySelector('input');
    input.type = input.type === 'password' ? 'text' : 'password';
  }
</script>

@endsection
