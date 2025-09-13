{{-- resources/views/campaigns/show.blade.php --}}
@extends('layouts.app', ['title' => 'Campaign Detail'])

@section('content')
  <div class="mb-4 flex items-center gap-2">
    <div>
      <h1 class="text-[18px] font-semibold">Campaign</h1>
      <p class="text-[12px] text-gray-500">Detailed view</p>
    </div>
    <div class="ml-auto flex items-center gap-2">
      <a href="{{ route('campaigns.edit', $campaign) }}"
         class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">Edit</a>
      <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}"
            onsubmit="return confirm('Delete this campaign?');">
        @csrf @method('DELETE')
        <button class="px-3 py-1.5 rounded-lg border bg-white text-rose-600 hover:bg-rose-50">Delete</button>
      </form>
    </div>
  </div>

  <div class="bg-white rounded-2xl border shadow-sm p-4 space-y-4 text-[14px]">
    <div>
      <div class="text-sm text-gray-500">Name</div>
      <div class="font-medium">{{ $campaign->name }}</div>
    </div>

    <div>
      <div class="text-sm text-gray-500">Type</div>
      <div class="font-medium capitalize">{{ $campaign->type }}</div>
    </div>

    @if($campaign->type === 'banner')
      <div>
        <div class="text-sm text-gray-500">Banner Text</div>
        <div class="font-medium">{{ $campaign->banner_text }}</div>
      </div>
    @endif

    @if($campaign->type === 'image')
      <div>
        <div class="text-sm text-gray-500 mb-1">Image</div>
        @if($campaign->imageUrl())
          <img src="{{ $campaign->imageUrl() }}"
               alt="Campaign Image"
               class="max-h-64 rounded-xl border">
          <div class="mt-1 text-xs text-gray-500">
            Size: {{ ucfirst($campaign->image_size ?? '—') }}
          </div>
        @else
          <div class="text-gray-500">—</div>
        @endif
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-sm text-gray-500">Start Date</div>
        <div class="font-medium">
          {{ $campaign->start_date?->format('M d, Y') ?? '—' }}
        </div>
      </div>
      <div>
        <div class="text-sm text-gray-500">End Date</div>
        <div class="font-medium">
          {{ $campaign->end_date?->format('M d, Y') ?? '—' }}
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-sm text-gray-500">Status</div>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border
          {{ $campaign->is_running
              ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
              : 'bg-gray-50 text-gray-600 border-gray-200' }}">
          {{ $campaign->status_label }}
        </span>
      </div>
      <div>
        <div class="text-sm text-gray-500">Views</div>
        <div class="font-medium">{{ number_format($campaign->views) }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-sm text-gray-500">Created</div>
        <div class="font-medium">{{ $campaign->created_at?->format('M d, Y H:i') ?? '—' }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Last Updated</div>
        <div class="font-medium">{{ $campaign->updated_at?->format('M d, Y H:i') ?? '—' }}</div>
      </div>
    </div>
  </div>
@endsection
