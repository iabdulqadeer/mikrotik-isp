@extends('layouts.app', ['title' => 'Campaigns'])

@section('content')

  <div class="mb-4">
    <h1 class="text-[18px] font-semibold">Campaigns</h1>
    <p class="text-[12px] text-gray-500">Create ads that are displayed to your clients once they connect to your WiFi network.</p>
  </div>

  <div class="mb-3 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <a href="{{ route('campaigns.index', ['tab'=>'running']) }}"
         class="px-3 py-1.5 rounded-xl border {{ ($tab==='running')?'border-indigo-600 text-indigo-700 bg-indigo-50':'border-gray-200 text-gray-600 bg-white' }}">
        <span class="inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Running <span class="ml-1 text-xs text-gray-500">({{ $runningCount }})</span>
        </span>
      </a>
      <a href="{{ route('campaigns.index', ['tab'=>'expired']) }}"
         class="px-3 py-1.5 rounded-xl border {{ ($tab==='expired')?'border-indigo-600 text-indigo-700 bg-indigo-50':'border-gray-200 text-gray-600 bg-white' }}">
        <span class="inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          Expired <span class="ml-1 text-xs text-gray-500">({{ $expiredCount }})</span>
        </span>
      </a>
    </div>

    <div class="flex items-center gap-2">
      <form method="GET" class="relative">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="q" value="{{ $s ?? '' }}" placeholder="Search"
               class="h-10 rounded-xl border border-gray-200 bg-white pl-9 pr-3">
        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
      </form>
      @can('campaigns.create')
        <a href="{{ route('campaigns.create') }}"
           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-orange-500 text-white hover:bg-orange-600">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
          Create Campaign
        </a>
      @endcan
    </div>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <table class="min-w-full">
      <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
        <tr>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Type</th>
          <th class="px-4 py-3">Views</th>
          <th class="px-4 py-3">Start Date</th>
          <th class="px-4 py-3">End Date</th>
          <th class="px-4 py-3 text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($campaigns as $c)
          <tr class="text-sm">
            <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
            <td class="px-4 py-3 capitalize">{{ $c->type }}</td>
            <td class="px-4 py-3">{{ number_format($c->views) }}</td>
            <td class="px-4 py-3">{{ $c->start_date?->format('M d, Y') }}</td>
            <td class="px-4 py-3">{{ $c->end_date?->format('M d, Y') ?? '—' }}</td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                @can('campaigns.view')
                  <a href="{{ route('campaigns.show',$c) }}"
                     class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">View</a>
                @endcan
                @can('campaigns.update')
                  <a href="{{ route('campaigns.edit',$c) }}"
                     class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">Edit</a>
                @endcan
                @can('campaigns.delete')
                  <form method="POST" action="{{ route('campaigns.destroy',$c) }}"
                        onsubmit="return confirm('Delete this campaign?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100">Delete</button>
                  </form>
                @endcan
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-16 text-center text-gray-500">
              <div class="flex flex-col items-center gap-2">
                <svg class="w-10 h-10 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <div class="font-medium">No campaigns found</div>
                <div class="text-sm">Create a campaign to start displaying ads.</div>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $campaigns->links() }}</div>
@endsection
