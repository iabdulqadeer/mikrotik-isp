{{-- resources/views/system-users/roles/index.blade.php --}}
@extends('layouts.app', ['title' => 'Manage Roles'])

@section('content')
  {{-- Page header --}}
  <div class="mb-4 flex items-center gap-2">
    <div>
      <h1 class="text-[18px] font-semibold">Manage Roles</h1>
      <p class="text-[12px] text-gray-500">Toggle permissions per role. Admin-only.</p>
    </div>
    <div class="ml-auto flex items-center gap-2">
      <a href="{{ route('systemusers.index') }}"
         class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Back to Users</a>
      <button form="rolesForm" type="submit"
              class="px-3 py-1.5 rounded-lg bg-orange-500 text-white hover:bg-orange-600">
        Save Changes
      </button>
    </div>
  </div>

  <form id="rolesForm" method="POST" action="{{ route('systemusers.roles.sync') }}"
        class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    @csrf
    @method('PUT')
    {{-- DESKTOP / TABLE VIEW --}}
<div class="hidden md:block">
  <div class="relative">
    {{-- Scroll container lives entirely below the sticky toolbar above --}}
    <div class="overflow-auto" id="rolesScroll">
      <table class="min-w-[920px] w-full text-sm">
        {{-- ✅ Header now sticks to the very top of the scroll area --}}
        <thead class="bg-gray-50 text-gray-700 sticky top-0 z-20 border-b">
          <tr>
            {{-- Sticky first header cell --}}
            <th class="sticky left-0 z-30 bg-gray-50 text-left py-2 px-3 font-medium w-[320px]">
              Permission
            </th>
            @foreach($roles as $r)
              <th class="py-2 px-3 font-medium text-center whitespace-nowrap min-w-[140px]">
                <div class="flex items-center justify-center gap-2">
                  <span>{{ strtoupper($r->name) }}</span>
                  {{-- Per-column check/uncheck --}}
                  <button type="button"
                          class="text-xs text-indigo-700 hover:underline"
                          onclick="toggleColumn({{ $loop->index }}, true)">all</button>
                  <span class="text-gray-300 text-xs">/</span>
                  <button type="button"
                          class="text-xs text-indigo-700 hover:underline"
                          onclick="toggleColumn({{ $loop->index }}, false)">none</button>
                </div>
              </th>
            @endforeach
          </tr>
        </thead>

        <tbody>
        @foreach($grouped as $group => $perms)
          {{-- Group label row renders UNDER the header as expected --}}
          <tr class="bg-gray-50/70 border-t">
            <td class="sticky left-0 z-10 bg-gray-50 px-4 py-2 text-[12px] font-semibold text-gray-700 uppercase tracking-wide"
                colspan="{{ 1 + $roles->count() }}">
              {{ $group }}
            </td>
          </tr>

          @foreach($perms as $p)
            <tr class="border-t hover:bg-gray-50/60">
              {{-- Sticky first column for each row --}}
              <td class="sticky left-0 z-10 bg-white py-2 px-4 align-top w-[320px]">
                <div class="font-medium text-gray-800 break-words">{{ $p->name }}</div>
                @if($p->description ?? false)
                  <div class="text-xs text-gray-500">{{ $p->description }}</div>
                @endif
              </td>

              {{-- Role cells --}}
              @foreach($roles as $r)
                <td class="py-2 px-3 text-center align-middle">
                  <label class="inline-flex items-center justify-center">
                    <input
                      type="checkbox"
                      name="permissions[{{ $r->id }}][]"
                      value="{{ $p->id }}"
                      class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 role-col-{{ $loop->index }}"
                      {{ in_array($p->id, $assigned[$r->id] ?? []) ? 'checked' : '' }}
                    >
                  </label>
                </td>
              @endforeach
            </tr>
          @endforeach
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>


    {{-- MOBILE / CARD VIEW --}}
    <div class="md:hidden">
      <div class="divide-y">
        @foreach($grouped as $group => $perms)
          <div class="bg-gray-50 px-4 py-2 text-[12px] font-semibold text-gray-700 uppercase tracking-wide">
            {{ $group }}
          </div>
          @foreach($perms as $p)
            <div class="p-4">
              <div class="font-medium text-gray-800 mb-2">{{ $p->name }}</div>
              <div class="grid grid-cols-2 gap-2">
                @foreach($roles as $r)
                  <label class="flex items-center gap-2 rounded-lg border px-3 py-2">
                    <input
                      type="checkbox"
                      name="permissions[{{ $r->id }}][]"
                      value="{{ $p->id }}"
                      class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      {{ in_array($p->id, $assigned[$r->id] ?? []) ? 'checked' : '' }}
                    >
                    <span class="text-xs font-medium">{{ strtoupper($r->name) }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          @endforeach
        @endforeach
      </div>
    </div>

    {{-- Hidden empties per role so we can clear all --}}
    @foreach($roles as $r)
      <input type="hidden" name="permissions[{{ $r->id }}][]" value="">
    @endforeach

    {{-- bottom bar --}}
    <div class="p-4 border-t bg-gray-50 flex justify-end">
      <button type="submit" class="rounded-xl bg-orange-500 text-white px-4 py-2 hover:bg-orange-600">
        Save Changes
      </button>
    </div>
  </form>

  {{-- Keep Blade out of this script to avoid directive parsing --}}
  @verbatim
  <script>
    function checkAll(state) {
      document.querySelectorAll('#rolesForm input[type="checkbox"]').forEach(cb => cb.checked = !!state);
    }
    // Toggle a single column (0-based column index matching role headers)
    function toggleColumn(colIndex, state) {
      document.querySelectorAll('.role-col-' + colIndex).forEach(cb => cb.checked = !!state);
    }
  </script>
  @endverbatim
@endsection
