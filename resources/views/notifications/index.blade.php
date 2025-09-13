@extends('layouts.app', ['title' => 'Notifications'])

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Notifications</h1>

    @can('notifications.mark_all')
      @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
          @csrf
          <button class="px-3 py-1.5 text-sm rounded-lg bg-gray-800 text-white hover:bg-gray-900">
            Mark all as read ({{ $unreadCount }})
          </button>
        </form>
      @endif
    @endcan
  </div>

  <div class="space-y-2">
    @forelse($notifications as $n)
      @php
        $isUnread = is_null($n->read_at);
        $d = $n->data ?? [];
      @endphp

      <div class="border rounded-xl p-3 bg-white flex items-start gap-3 {{ $isUnread ? 'ring-2 ring-indigo-100' : '' }}">
        <div class="mt-0.5">
          <span class="inline-block w-2 h-2 rounded-full {{ $isUnread ? 'bg-indigo-600' : 'bg-gray-300' }}"></span>
        </div>

        <div class="flex-1">
          <div class="text-sm font-medium">{{ $d['title'] ?? 'Notification' }}</div>
          @if(!empty($d['message']))
            <div class="text-sm text-gray-600 mt-0.5">{{ $d['message'] }}</div>
          @endif
          <div class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
        </div>

        <div class="flex items-center gap-2">
          @can('notifications.view')
            @if(!empty($d['action_url']))
              <a href="{{ route('notifications.show', $n->id) }}"
                 class="text-[12px] px-2 py-1 rounded-lg border hover:bg-gray-50">
                Open
              </a>
            @endif
          @endcan

          @can('notifications.mark_read')
            @if($isUnread)
              <form method="POST" action="{{ route('notifications.markRead', $n->id) }}">
                @csrf
                <button class="text-[12px] px-2 py-1 rounded-lg border hover:bg-gray-50">
                  Mark read
                </button>
              </form>
            @endif
          @endcan
        </div>
      </div>
    @empty
      <div class="text-sm text-gray-500">No notifications yet.</div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $notifications->links() }}
  </div>
</div>
@endsection
