{{-- overlay for mobile --}}
<div
  data-sidebar-overlay
  class="fixed inset-0 z-[45] bg-black/40 backdrop-blur-sm hidden lg:hidden">
</div>

@php
  /** @var \App\Models\User|null $user */
  $user = auth()->user();

  /** Visible (role+permission filtered) menu tree */
  $menuTree = \App\Models\Menu::treeForUser($user) ?? collect();

  /**
   * Guarded link renderer
   * - proper lucide-style icons
   * - active state
   * - count pill
   */
  function sb_link_guarded($label,$href,$active,$icon,$ability=null,$count=0){
    $user = auth()->user();
    $isAdmin = $user?->hasRole('admin');

    // Admin bypasses ability checks completely
    if (!$isAdmin && $ability && !($user && $user->can($ability))) return;

    $base = 'mx-2 my-0.5 flex items-center gap-3 px-3 h-11 rounded-xl transition';
    $cls  = $active ? "$base bg-orange-100 text-orange-900" : "$base hover:bg-gray-50 text-slate-700";

    $ico  = match($icon){
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

    echo '<a href="'.$href.'" class="'.$cls.'" '.($active?'aria-current="page"':'').'>
            <span class="w-5 h-5 shrink-0 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">'.$ico.'</svg>
            </span>
            <span class="truncate">'.$label.'</span>'.
            ($count ? '<span class="ml-auto inline-flex items-center justify-center h-5 min-w-[1.25rem] rounded-full text-[10px] px-1.5 '.($active?'bg-orange-500 text-white':'bg-orange-100 text-orange-700').'">'.$count.'</span>' : '').
            ($active ? '<span class="ml-2 h-5 w-1 rounded-full bg-orange-500/80"></span>' : '').
          '</a>';
  }
@endphp

<aside
  data-sidebar
  class="fixed inset-y-0 left-0 z-50 w-[260px] shrink-0 bg-white border-r border-gray-200
         shadow-lg -translate-x-full transition-transform duration-200 ease-in-out will-change-transform
         lg:translate-x-0 lg:sticky lg:top-0 lg:self-start lg:z-30 lg:shadow-none">
  <div class="h-dvh lg:h-screen flex flex-col">

    {{-- Header --}}
    <div class="h-14 md:h-16 px-4 flex items-center border-b border-gray-200">
      <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0 py-5 ms-5">
        <img src="{{ asset('logo_transparent.png') }}" alt="App Logo" class="h-8 w-auto" loading="lazy">
      </a>
      <button type="button" data-sidebar-toggle
              class="ml-auto lg:hidden p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              aria-label="Open sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-1 py-3 text-gray-700">
      @foreach ($menuTree as $item)
        @php
          $hasLink = $item->route_name || $item->url;
        @endphp

        @if (!$hasLink)
          {{-- Category header --}}
          <div class="mt-6 mb-2 px-4 text-[12px] font-semibold uppercase tracking-wide text-gray-400">
            {{ $item->label }}
          </div>
        @else
          {{-- Standalone clickable item (e.g., Dashboard) --}}
          @php
            $href   = $item->route_name ? route($item->route_name) : ($item->url ?? '#');
            $active = $item->route_name
                ? request()->routeIs($item->route_name)
                : ($item->url ? request()->is(ltrim($item->url,'/').'*') : false);
          @endphp
          {!! sb_link_guarded($item->label, $href, $active, $item->icon, $item->permission, (int)($item->badge_count ?? 0)) !!}
        @endif

        {{-- Children under either a header or a clickable parent --}}
        @php
          // Use visible_children if provided; otherwise fall back to eagerly-loaded children
          $children = collect($item->visible_children ?? null);
          if ($children->isEmpty() && isset($item->children)) {
              $children = $item->children instanceof \Illuminate\Support\Collection
                  ? $item->children
                  : collect($item->children);
          }
        @endphp

        @if ($children->count())
          <div class="{{ $hasLink ? 'ml-4' : '' }}">
            @foreach ($children as $child)
              @php
                $chref   = $child->route_name ? route($child->route_name) : ($child->url ?? '#');
                $cactive = $child->route_name
                    ? request()->routeIs($child->route_name)
                    : ($child->url ? request()->is(ltrim($child->url,'/').'*') : false);
              @endphp
              {!! sb_link_guarded($child->label, $chref, $cactive, $child->icon, $child->permission, (int)($child->badge_count ?? 0)) !!}
            @endforeach
          </div>
        @endif
      @endforeach
    </nav>
  </div>
</aside>
