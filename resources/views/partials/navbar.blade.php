<header class="fixed top-0 right-0 left-0 bg-white/90 backdrop-blur border-b isolate z-[200] lg:left-[250px]">
  <div class="max-w-7xl mx-auto h-14 px-3 md:h-16 md:px-6 flex items-center gap-3">
    {{-- Mobile menu button --}}
    <button data-sidebar-toggle class="lg:hidden p-2 rounded-lg hover:bg-gray-100 shrink-0" aria-label="Toggle menu">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    {{-- Search --}}
    <div class="flex-1 min-w-0 sm:max-w-3xl sm:mx-auto">
      <label class="relative block">
        <span class="absolute inset-y-0 left-3 flex items-center">
          <svg class="w-4.5 h-4.5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-3.5-3.5"/>
          </svg>
        </span>
        <input
          type="search"
          placeholder="Search…"
          class="w-full pl-9 pr-3 h-10 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-gray-300 outline-none text-sm"
        >
      </label>
    </div>

     @php
      /**
       * Reuse the same icon set as the sidebar, but with sane defaults for size & stroke
       */
      function ui_svg_icon(string $icon, string $classes = 'w-5 h-5 text-gray-500'): string {
        $paths = match($icon){
          'dashboard'  => '<rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>',
          'activity'   => '<path d="M22 12h-4l-3 9L9 3 6 12H2"/>',
          'users'      => '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>',
          'ticket'     => '<path d="M3 7h18v6a2 2 0 0 1-2 2h-3v2H8v-2H5a2 2 0 0 1-2-2V7z"/><path d="M15 7v8"/>',
          'user-plus'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/>',
          'layers'     => '<path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/>',
          'money'      => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/>',
          'receipt'    => '<path d="M4 2h16v20l-2-1-2 1-2-1-2 1-2-1-2 1-2-1z"/><path d="M8 7h8"/><path d="M8 11h8"/>',
          'message'    => '<path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>',
          'mail'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
          'megaphone'  => '<path d="M3 11v2a4 4 0 0 0 4 4h2v-4"/><path d="M21 8v8"/><path d="M7 9l10-5v16l-10-5V9z"/>',
          'server'     => '<rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 8h.01M7 18h.01"/>',
          'hard-drive' => '<rect x="3" y="7" width="18" height="10" rx="2"/><line x1="7" y1="11" x2="7" y2="11"/><line x1="11" y1="11" x2="11" y2="11"/>',
          default      => '<circle cx="12" cy="12" r="9"/>'
        };

        return '<svg class="'.$classes.' shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths.'</svg>';
      }

      // Notifications data (show latest 10 unread)
      /** @var \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Support\Collection $__notes */
      $__notes = auth()->user()
          ? auth()->user()->unreadNotifications()->latest()->take(10)->get()
          : collect();

      $__unreadCount = $__notes->count();
    @endphp

    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
      {{-- ========================== --}}
      {{-- Notifications Dropdown     --}}
      {{-- ========================== --}}
      <div class="relative">
        <details class="group">
          <summary class="list-none cursor-pointer p-1.5 rounded-lg hover:bg-gray-100 flex items-center">
            {{-- Bell icon --}}
            <svg class="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M14 21a2 2 0 0 1-4 0"/><path d="M6 8a6 6 0 1 1 12 0c0 7 3 5 3 8H3c0-3 3-1 3-8"/>
            </svg>

            {{-- Badge --}}
            @if($__unreadCount)
              <span class="ml-1 inline-flex min-w-[18px] h-[18px] items-center justify-center rounded-full bg-rose-600 text-white text-[10px] px-1">
                {{ $__unreadCount }}
              </span>
            @endif
          </summary>

          <div class="absolute right-0 mt-2 w-80 bg-white border rounded-2xl shadow-lg overflow-hidden z-50">
            <div class="flex items-center justify-between px-3 py-2 border-b">
              <div class="text-sm font-semibold">Notifications</div>
              @if($__unreadCount)
                {{-- Optional: mark all read (expects a route) --}}
                <form method="POST" action="{{ route('notifications.readAll') }}">
                  @csrf @method('PATCH')
                  <button class="text-[11px] text-indigo-600 hover:underline">Mark all as read</button>
                </form>
              @endif
            </div>

            <ul class="max-h-80 overflow-y-auto divide-y text-sm">
              @forelse($__notes as $note)
                <li class="p-3 hover:bg-gray-50">
                  <div class="flex items-start gap-2">
                    <div class="mt-0.5">
                      {{-- small dot for unread --}}
                      <span class="inline-block w-2 h-2 rounded-full bg-rose-500"></span>
                    </div>
                    <div class="min-w-0">
                      <div class="font-medium truncate">
                        {{ data_get($note->data, 'title', 'Notification') }}
                      </div>
                      @if(data_get($note->data, 'message'))
                        <div class="text-xs text-gray-600">
                          {{ data_get($note->data, 'message') }}
                        </div>
                      @endif
                      <div class="text-[11px] text-gray-400 mt-1">
                        {{ $note->created_at->diffForHumans() }}
                      </div>

                      <div class="mt-2 flex items-center gap-2">
                        @if($url = data_get($note->data, 'url'))
                          <a href="{{ $url }}" class="text-[11px] text-indigo-600 hover:underline">Open</a>
                        @endif
                        {{-- Optional: mark single read (expects a route) --}}
                        <form method="POST" action="{{ route('notifications.read', $note->id) }}">
                          @csrf @method('PATCH')
                          <button class="text-[11px] text-gray-500 hover:text-gray-700">Mark read</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </li>
              @empty
                <li class="p-3 text-center text-gray-500">No new notifications</li>
              @endforelse
            </ul>

            <div class="p-2 border-t text-center">
              <a href="{{ route('notifications.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
            </div>
          </div>
        </details>
      </div>
      {{-- ========================== --}}

      {{-- user dropdown --}}
      <div class="relative">
        <details class="group">
          <summary class="list-none cursor-pointer flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100">
            <span class="hidden sm:inline text-[13px]">{{ auth()->user()->name ?? 'Account' }}</span>
            <span class="w-8 h-8 rounded-full bg-gray-200 grid place-items-center text-[13px] font-medium">
              {{ strtoupper(mb_substr(auth()->user()->name ?? 'U',0,1)) }}
            </span>
          </summary>

          <div class="absolute right-0 mt-2 w-56 bg-white border rounded-xl shadow-lg overflow-hidden py-1">

            {{-- Account Settings --}}
            @can('settings.view')
              <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50">
                {!! ui_svg_icon('dashboard') !!}
                <span>Account Settings</span>
              </a>
            @endcan

            {{-- Billing & Subscriptions --}}
            @can('subscriptions.index')
              <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50">
                {!! ui_svg_icon('money') !!}
                <span>Billing & Subscriptions</span>
              </a>
            @endcan

            {{-- System Users --}}
            @can('systemusers.list')
              <a href="{{ route('systemusers.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50">
                {!! ui_svg_icon('users') !!}
                <span>System Users</span>
              </a>
            @endcan

            {{-- Contact Support --}}
            @can('tickets.list')
              <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50">
                {!! ui_svg_icon('ticket') !!}
                <span>Contact Support</span>
              </a>
            @endcan

            <div class="my-1 h-px bg-gray-100"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="w-full flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50">
                {!! ui_svg_icon('activity') !!}
                <span>Logout</span>
              </button>
            </form>
          </div>
        </details>
      </div>
    </div>
  </div>
</header>
