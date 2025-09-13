@extends('layouts.app', ['title' => 'Active Users'])

@section('content')
  @include('partials.flash')

  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Active Users</h1>
    <p class="text-[12px] text-gray-500">Live sessions across Hotspot and PPPoE.</p>
  </div>

  <div class="mb-3 flex flex-col gap-3 md:flex-row md:items-center">
    <div class="flex items-center gap-3">
      @php
        $activeTab = $type ?? 'all';
        $tabUrl = function($t) {
          return route('users.active', array_filter(['type' => $t ?: null, 'q' => request('q')]));
        };
      @endphp

      <a href="{{ $tabUrl(null) }}"
         class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {{ $activeTab==='all' ? 'border-orange-300 bg-orange-50 text-orange-700' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>All</span>
        <span class="ml-1 text-[11px] px-1.5 rounded bg-white/70 border">{{ $counts['all'] ?? 0 }}</span>
      </a>

      <a href="{{ $tabUrl('hotspot') }}"
         class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {{ $activeTab==='hotspot' ? 'border-orange-300 bg-orange-50 text-orange-700' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-width="2" d="M12 20a8 8 0 0 0 8-8M4 12a8 8 0 0 1 8-8m-6 8a6 6 0 0 1 6-6m0 12a6 6 0 0 0 6-6"/></svg>
        <span>Hotspot</span>
        <span class="ml-1 text-[11px] px-1.5 rounded bg-white/70 border">{{ $counts['hotspot'] ?? 0 }}</span>
      </a>

      <a href="{{ $tabUrl('pppoe') }}"
         class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {{ $activeTab==='pppoe' ? 'border-orange-300 bg-orange-50 text-orange-700' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="14" rx="3" ry="3" stroke="currentColor" stroke-width="2"/><path d="M7 20h10" stroke="currentColor" stroke-width="2"/></svg>
        <span>PPPoE</span>
        <span class="ml-1 text-[11px] px-1.5 rounded bg-white/70 border">{{ $counts['pppoe'] ?? 0 }}</span>
      </a>
    </div>

    <form method="GET" class="md:ml-auto flex items-center gap-2">
      <input type="hidden" name="type" value="{{ $type }}">
      <input name="q" value="{{ $q }}"
             class="h-10 w-64 rounded-xl border border-gray-200 bg-white px-3"
             placeholder="Search username / IP / MAC / router…">
      <button class="h-10 px-4 rounded-xl border bg-white hover:bg-gray-50">Search</button>
      @if(request()->hasAny(['q','type']))
        <a href="{{ route('users.active') }}" class="h-10 px-3 rounded-xl border hover:bg-gray-50">Reset</a>
      @endif
    </form>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-[14px]">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">Username</th>
            <th class="px-4 py-3 text-left">IP/MAC</th>
            <th class="px-4 py-3 text-left">Router</th>
            <th class="px-4 py-3 text-left">Session Start</th>
            <th class="px-4 py-3 text-left">Session End</th>
          </tr>
        </thead>

        @if(($rows ?? collect())->isEmpty())
          <tbody>
            <tr>
              <td colspan="5" class="px-4 py-12">
                <div class="grid place-items-center text-gray-500">
                  <div class="text-center">
                    <div class="mx-auto w-10 h-10 border-2 border-dashed border-gray-300 rounded-full"></div>
                    <div class="mt-2 text-[13px] font-medium">No Active Users</div>
                    <div class="text-[12px]">There are no active users at the moment.</div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        @else
          <tbody class="divide-y">
            @foreach($rows as $r)
              <tr class="hover:bg-gray-50/60">
                <td class="px-4 py-3 font-medium">
                  {{ $r['username'] ?? '—' }}
                  @if(($r['type'] ?? null) === 'hotspot')
                    <span class="ml-2 text-[11px] px-2 py-0.5 rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200">Hotspot</span>
                  @elseif(($r['type'] ?? null) === 'pppoe')
                    <span class="ml-2 text-[11px] px-2 py-0.5 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200">PPPoE</span>
                  @endif
                </td>
                <td class="px-4 py-3">
                  <div>{{ $r['ip'] ?? '—' }}</div>
                  <div class="text-[12px] text-gray-500">{{ $r['mac'] ?? '—' }}</div>
                </td>
                <td class="px-4 py-3">{{ $r['router'] ?? '—' }}</td>
                <td class="px-4 py-3">
                  @if(!empty($r['session_start']))
                    <span class="text-[12px] text-gray-700">{{ $r['session_start']->format('Y-m-d H:i') }}</span>
                    <span class="ml-1 text-[12px] text-gray-500">({{ $r['session_start']->diffForHumans() }})</span>
                  @else
                    <span class="text-[12px] text-gray-500">—</span>
                  @endif
                </td>
                <td class="px-4 py-3"><span class="text-[12px] text-gray-500">—</span></td>
              </tr>
            @endforeach
          </tbody>
        @endif
      </table>
    </div>

    @if(($pager['has_more'] ?? false))
      <div class="px-4 py-3 border-t">
        @php
          $nextParams = array_filter([
            'type'     => $type,
            'q'        => $q,
            'page'     => ($pager['current'] ?? 1) + 1,
            'per_page' => $pager['per_page'] ?? 20,
          ]);
        @endphp
        <a href="{{ route('users.active', $nextParams) }}" class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Load more</a>
      </div>
    @endif
  </div>
@endsection
