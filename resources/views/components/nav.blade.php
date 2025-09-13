@props(['href' => '#', 'active' => false, 'icon' => null])

@php
$base = 'mx-3 my-1 flex items-center gap-3 px-3 py-2 rounded-lg transition';
$cls = $active ? "$base bg-indigo-50 text-indigo-700" : "$base text-gray-700 hover:bg-gray-50";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>
  <span class="w-5 h-5">
    @switch($icon)
      @case('home')    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m3 12 9-9 9 9M4 10v10h16V10"/></svg>@break
      @case('users')   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13A4 4 0 0 1 19 7"/></svg>@break
      @case('layers')  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/></svg>@break
      @case('ticket')  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h18v6a2 2 0 0 1-2 2h-3v2H8v-2H5a2 2 0 0 1-2-2V7z"/><path d="M15 7v8"/></svg>@break
      @case('money')   <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg>@break
      @case('arrows')  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m7 7 5-5 5 5M7 17l5 5 5-5"/><path d="M12 2v20"/></svg>@break
      @case('wallet')  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M18 12h.01"/></svg>@break
      @case('chart')   <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18"/><rect x="7" y="12" width="3" height="6"/><rect x="12" y="9" width="3" height="9"/><rect x="17" y="5" width="3" height="13"/></svg>@break
      @case('id')      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h8M7 12h8M7 16h5"/></svg>@break
      @case('sms')     <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>@break
      @case('server')  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 8h.01M7 18h.01"/></svg>@break
      @case('support') <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9"/><path d="M8 15a4 4 0 1 1 8 0v2H8z"/></svg>@break
      @case('sub')     <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="8" width="18" height="8" rx="2"/><path d="M7 8V6a5 5 0 0 1 10 0v2"/></svg>@break
      @case('settings')<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 1v3M12 20v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M1 12h3M20 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12"/><circle cx="12" cy="12" r="3"/></svg>@break
    @endswitch
  </span>
  <span class="text-sm">{{ $slot }}</span>
  <span class="ml-auto text-[10px] text-gray-400">0</span>
</a>
