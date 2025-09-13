<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ ($title ?? ($code ?? 'Error')) }} • {{ config('app.name', 'Laravel') }}</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    .tracking-widest {
    letter-spacing: 0.1em !important;
    font-size: 5rem !important;
}
  </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
  <div class="min-h-screen grid place-items-center px-4">
    <main class="w-full max-w-3xl">
      <section class="relative bg-white rounded-[20px] border border-gray-200 shadow-sm px-6 py-12 md:px-10 text-center overflow-hidden">
        @php $accent = 'emerald'; @endphp

        {{-- Dynamic hero art --}}
        <div class="mx-auto mb-4 w-44 h-24 relative select-none pointer-events-none">
          @switch((int)($code ?? 0))
            @case(404)
              <div class="absolute inset-0 grid place-items-center">
                <div class="font-black tracking-widest text-6xl text-{{ $accent }}-500/90 leading-none">404</div>
              </div>
              <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                {{-- Document + magnifier --}}
                <svg class="w-12 h-12 text-{{ $accent }}-500" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                  <rect x="14" y="6" width="30" height="40" rx="3" stroke="currentColor" stroke-width="2" fill="white"/>
                  <path d="M38 14H20M38 22H20M30 30H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <circle cx="44" cy="42" r="8" stroke="currentColor" stroke-width="2"/>
                  <path d="M49 47l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="absolute bottom-0 inset-x-8 h-2 rounded-full bg-{{ $accent }}-100/80"></div>
            @break

            @case(401)
              <div class="grid place-items-center">
                {{-- Lock --}}
                <svg class="w-12 h-12 text-{{ $accent }}-500" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="10" width="18" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M7 10V7a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2"/>
                </svg>
              </div>
            @break

            @case(403)
              <div class="grid place-items-center">
                {{-- Shield/ban --}}
                <svg class="w-12 h-12 text-{{ $accent }}-500" viewBox="0 0 24 24" fill="none">
                  <path d="M12 3l7 4v5c0 5-3.5 7.5-7 9-3.5-1.5-7-4-7-9V7l7-4z" stroke="currentColor" stroke-width="2"/>
                  <path d="M9 9l6 6M15 9l-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
            @break

            @case(419)
              <div class="grid place-items-center">
                {{-- Clock/expiry --}}
                <svg class="w-12 h-12 text-{{ $accent }}-500" viewBox="0 0 24 24" fill="none">
                  <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 7v5l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
            @break

            @case(422)
              <div class="grid place-items-center">
                {{-- Doc with X --}}
                <svg class="w-12 h-12 text-{{ $accent }}-500" viewBox="0 0 24 24" fill="none">
                  <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <path d="M9 16l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
            @break

            @case(429)
              <div class="grid place-items-center">
                {{-- Speedometer --}}
                <svg class="w-12 h-12 text-{{ $accent }}-500" viewBox="0 0 24 24" fill="none">
                  <path d="M6 19a8 8 0 1 1 12 0" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 13l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
            @break

            @case(500)
            @case(503)
              <div class="grid place-items-center">
                {{-- Triangle warning --}}
                <svg class="w-12 h-12 text-{{ $accent }}-500" viewBox="0 0 24 24" fill="none">
                  <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2"/>
                  <path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
            @break

            @default
              <div class="grid place-items-center">
                {{-- Generic bubble --}}
                <div class="w-16 h-16 rounded-2xl bg-{{ $accent }}-50 border border-{{ $accent }}-200 text-{{ $accent }}-600 grid place-items-center">
                  <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </div>
              </div>
          @endswitch
        </div>

        {{-- Title & subtitle --}}
        <h1 class="text-[26px] md:text-[28px] font-semibold">
          {{ $title ?? 'Something went wrong' }}
        </h1>
        <p class="mt-2 text-[14px] md:text-[15px] text-gray-500">
          {{ $description ?? 'The page you are looking for is not available or an error occurred.' }}
        </p>

        {{-- Primary action --}}
        <div class="mt-6">
          @php $homeUrl = Route::has('dashboard') ? route('dashboard') : url('/'); @endphp
          <a href="{{ $homeUrl }}"
             class="inline-flex items-center justify-center h-11 px-6 rounded-full bg-{{ $accent }}-500 text-dark hover:bg-{{ $accent }}-600 transition">
            GO HOME
          </a>
        </div>

        {{-- Optional tiny debug (local only) --}}
        @if(app()->environment('local') && !empty($debug))
          <div class="mt-6 text-left rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 text-xs">
            <div class="font-semibold mb-1">Debug info</div>
            <pre class="whitespace-pre-wrap break-words">{{ $debug }}</pre>
          </div>
        @endif
      </section>
    </main>
  </div>
</body>
</html>
