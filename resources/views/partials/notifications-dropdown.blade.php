@php
  /** @var \App\Models\User $authUser */
  $authUser = auth()->user();
  $unreadCount = $authUser?->unreadNotifications()->count() ?? 0;
  $latest = $authUser?->notifications()->latest()->limit(8)->get() ?? collect();
@endphp

<div class="relative">
  <details class="group">
    <summary class="list-none cursor-pointer p-2 rounded-lg hover:bg-gray-100 relative">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
      </svg>
      @if($unreadCount > 0)
        <span class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] text-[11px] leading-[1.1rem] text-white bg-red-600 rounded-full text-center px-1">
          {{ $unreadCount }}
        </span>
      @endif
    </summary>

    <div class="absolute right-0 mt-2 w-80 bg-white border rounded-xl shadow-lg overflow-hidden z-50">

      <div class="px-3 py-2 border-b flex items-center justify-between">
        <div class="text-sm font-medium">Notifications</div>
        @if($unreadCount > 0)
          <form method="POST" action="{{ route('notifications.markAllRead') }}">
            @csrf
            <button class="text-[12px] px-2 py-1 rounded-lg border hover:bg-gray-50">
              Mark all read
            </button>
          </form>
        @endif
      </div>

      <div class="max-h-96 overflow-auto divide-y">
        @forelse($latest as $n)
          @php
            $isUnread = is_null($n->read_at);
            $d = $n->data ?? [];
          @endphp
          <div class="px-3 py-2 flex items-start gap-3 {{ $isUnread ? 'bg-indigo-50/40' : '' }}">
            <div class="mt-1">
              <span class="inline-block w-1.5 h-1.5 rounded-full {{ $isUnread ? 'bg-indigo-600' : 'bg-gray-300' }}"></span>
            </div>
            <div class="flex-1">
              <div class="text-[13px] font-medium">{{ $d['title'] ?? 'Notification' }}</div>
              @if(!empty($d['message']))
                <div class="text-[12px] text-gray-600 mt-0.5 line-clamp-2">{{ $d['message'] }}</div>
              @endif
              <div class="text-[11px] text-gray-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            <div class="flex items-center gap-2">
              @if(!empty($d['action_url']))
                <a href="{{ route('notifications.show', $n->id) }}"
                   class="text-[12px] px-2 py-1 rounded-lg border hover:bg-gray-50">
                  Open
                </a>
              @endif
              @if($isUnread)
                <button
                  data-mark-read
                  data-url="{{ route('notifications.markRead', $n->id) }}"
                  class="text-[12px] px-2 py-1 rounded-lg border hover:bg-gray-50">
                  Read
                </button>
              @endif
            </div>
          </div>
        @empty
          <div class="px-3 py-6 text-sm text-gray-500 text-center">No notifications yet.</div>
        @endforelse
      </div>

      <div class="px-3 py-2 border-t">
        <a class="block text-center text-[13px] px-3 py-1.5 rounded-lg border hover:bg-gray-50"
           href="{{ route('notifications.index') }}">
          View all
        </a>
      </div>
    </div>
  </details>
</div>

{{-- Tiny inline JS to mark-as-read without leaving the dropdown --}}
<script>
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('[data-mark-read]');
  if (!btn) return;
  try {
    const url = btn.getAttribute('data-url');
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      }
    });
    if (res.ok) {
      // visually mute the row
      const row = btn.closest('div.px-3.py-2');
      row && row.classList.remove('bg-indigo-50/40');
      // remove button
      btn.remove();
      // decrement badge
      const badge = document.querySelector('summary > span.absolute');
      if (badge) {
        let n = parseInt(badge.textContent || '0', 10);
        n = Math.max(0, (n || 0) - 1);
        if (n === 0) badge.remove(); else badge.textContent = n;
      }
    }
  } catch (err) { /* no-op */ }
});
</script>
