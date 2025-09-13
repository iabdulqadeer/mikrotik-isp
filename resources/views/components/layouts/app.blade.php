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
  </style>
</head>
<body class="bg-[#F6F7FB] text-[14px] text-gray-800">

  {{-- Top navbar --}}
  @include('partials.navbar')

  <div class="min-h-screen flex">
    {{-- Sidebar (with responsive toggler implemented in the partial) --}}
    @include('partials.sidebar')

    {{-- Page content --}}
    <main class="flex-1 px-3 md:px-6 py-4">
      <div class="max-w-7xl mx-auto">
        @yield('content')
      </div>
    </main>
  </div>

  <footer class="px-6 py-6 text-[12px] text-gray-500">
    <div class="max-w-7xl mx-auto">
      © {{ date('Y') }} {{ config('app.name') }} — All rights reserved.
    </div>
  </footer>

  {{-- super tiny, framework-free toggler fallback (in case Alpine isn’t present) --}}
  <script>
    document.addEventListener('DOMContentLoaded',function(){
      const btn = document.querySelector('[data-sidebar-toggle]');
      const sb  = document.querySelector('[data-sidebar]');
      const ov  = document.querySelector('[data-sidebar-overlay]');
      if(!btn || !sb || !ov) return;
      function close(){ sb.classList.add('-translate-x-full'); ov.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
      function open(){ sb.classList.remove('-translate-x-full'); ov.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
      btn.addEventListener('click',()=> sb.classList.contains('-translate-x-full') ? open() : close());
      ov.addEventListener('click', close);
      window.addEventListener('resize',()=>{ if(window.innerWidth>=1024){ sb.classList.remove('-translate-x-full'); ov.classList.add('hidden'); } });
    });
  </script>
</body>
</html>
