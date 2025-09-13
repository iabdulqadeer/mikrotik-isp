<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $title ?? config('app.name', 'MikroTik ISP Manager') }}</title>

  {{-- Inter font for clean enterprise look --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    html,body{ font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol' }

    [type='text'], input:where(:not([type])), [type='email'], [type='url'], [type='password'], [type='number'], [type='date'], [type='datetime-local'], [type='month'], [type='search'], [type='tel'], [type='time'], [type='week'], [multiple], textarea, select {
    border-color: #e5e7eb;
}

html { -webkit-text-size-adjust: 100%; } /* avoid odd zoom scaling on iOS */
:root { color-scheme: light; }
.scrollbar-thin::-webkit-scrollbar { width: 8px; }
.scrollbar-thin::-webkit-scrollbar-thumb { border-radius: 8px; }

  /* Mobile-safe full height */
  .h-dvh { height: 100vh; height: 100dvh; }
  /* iOS bottom inset */
  .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }

  .text-indigo-700 {
    --tw-text-opacity: 1;
    color: rgb(240, 100, 16)!important;
}
.bg-indigo-600\/70 {
    background-color: rgb(240 100 16 / 0.7)!important;
}
  </style>

  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
  (function(){
    try{
      var t = localStorage.getItem('theme') || 'system';
      var d = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (t === 'dark' || (t === 'system' && d)) document.documentElement.classList.add('dark');
    }catch(e){}
  })();
</script>

</head>

<body class="bg-[#F6F7FB] text-[14px] text-gray-80">
  @include('partials.navbar')

  <div class="min-h-screen flex">
    {{-- Sidebar + overlay (off-canvas on mobile) --}}
    @include('partials.sidebar')

    {{-- Content area --}}
    <main class="flex-1 min-w-0 px-3 md:px-6 pt-14 md:pt-16 pb-4">
      <div class="max-w-7xl mx-auto">
        @if(session()->has('impersonator_id'))
          <div class="bg-amber-50 border-b border-amber-200 text-amber-800 mb-3 rounded">
            <div class="max-w-7xl mx-auto px-4 py-2 flex items-center gap-3">
              <span>You are impersonating another account.</span>
              <form method="POST" action="{{ route('users.impersonate.leave') }}" class="ml-auto">
                @csrf
                <button class="h-8 px-3 rounded-lg bg-amber-600 text-white hover:bg-amber-700">
                  Exit Impersonation
                </button>
              </form>
            </div>
          </div>
        @endif
        <div class="mt-5"></div>
        @include('partials.flash')
        <div class="mt-5"></div>

        @yield('content')
      </div>
    </main>
  </div>

  {{-- Keep your tiny toggler JS exactly as you had it --}}
  <script>
(function () {
  function ready(fn){ if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

  ready(function () {
    const sb = document.querySelector('[data-sidebar]');
    const ov = document.querySelector('[data-sidebar-overlay]');
    const toggles = document.querySelectorAll('[data-sidebar-toggle]');
    if (!sb || !ov) return;

    const CLOSED = '-translate-x-full';

    function isOpen(){ return !sb.classList.contains(CLOSED); }
    function open(){
      sb.classList.remove(CLOSED);
      ov.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }
    function close(){
      sb.classList.add(CLOSED);
      ov.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
    function toggle(){ isOpen() ? close() : open(); }

    // attach
    toggles.forEach(btn => btn.addEventListener('click', toggle, { passive: true }));
    ov.addEventListener('click', close, { passive: true });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

    // initial state by breakpoint
    function applyByBp(){
      if (window.innerWidth >= 1024) {
        sb.classList.remove(CLOSED);
        ov.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      } else {
        sb.classList.add(CLOSED);
        ov.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
    }
    applyByBp();
    window.addEventListener('resize', applyByBp);
  });
})();
</script>


<script>
  (function () {
    const STORAGE_KEY = 'theme'; // values: 'light' | 'dark' | 'system'

    // apply theme to <html>
    function applyTheme(choice) {
      const root = document.documentElement;
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

      let isDark = false;
      if (choice === 'dark') isDark = true;
      else if (choice === 'light') isDark = false;
      else isDark = prefersDark; // system

      root.classList.toggle('dark', isDark);
    }

    // refresh UI active states
    function refreshUI(choice) {
      const wrap = document.getElementById('theme-toggle');
      if (!wrap) return;
      wrap.querySelectorAll('[data-theme-choice]').forEach(btn => {
        const active = btn.getAttribute('data-theme-choice') === choice;
        btn.setAttribute('data-active', String(active));

        // icon color
        btn.querySelectorAll('svg').forEach(svg => {
          svg.style.color = active ? '#EF7D00' /* orange-ish like your screenshot */ : '#9CA3AF'; /* gray-400 */
        });
      });
    }

    // read saved
    function getSaved() {
      return localStorage.getItem(STORAGE_KEY) || 'system';
    }

    // save & apply
    function setChoice(choice) {
      localStorage.setItem(STORAGE_KEY, choice);
      applyTheme(choice);
      refreshUI(choice);
    }

    // initial
    const initial = getSaved();
    applyTheme(initial);
    window.addEventListener('DOMContentLoaded', () => refreshUI(initial));

    // handle clicks
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-theme-choice]');
      if (!btn) return;
      const choice = btn.getAttribute('data-theme-choice');
      setChoice(choice);
    });

    // if “system”, react to OS changes
    const mq = window.matchMedia('(prefers-color-scheme: dark)');
    mq.addEventListener?.('change', () => {
      if (getSaved() === 'system') applyTheme('system');
    });
  })();
</script>


</body>


</html>
