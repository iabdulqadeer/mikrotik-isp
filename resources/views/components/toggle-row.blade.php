@props([
  'title',
  'subtitle' => null,
  'name',
  'checked' => false,
  'icon' => null,        // SVG path string for the icon
  'iconColor' => 'text-indigo-600',
  'id' => null,
])

@php $id = $id ?: $name; @endphp

<div class="flex items-start justify-between rounded-xl border px-4 py-3 bg-white">
  {{-- Left: icon + title + subtitle --}}
  <div class="flex items-start gap-3">
    <div class="mt-0.5">
      <svg class="w-5 h-5 {{ $iconColor }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path d="{{ $icon }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>
    <div>
      <div class="text-[14px] font-semibold text-slate-900">{{ $title }}</div>
      @if($subtitle)
        <div class="text-[12px] text-slate-500">{{ $subtitle }}</div>
      @endif
      {{-- for nested slot content (e.g., threshold input) --}}
      @if(trim($slot) !== '')
        <div class="mt-3">
          {{ $slot }}
        </div>
      @endif
    </div>
  </div>

  {{-- Right: orange switch --}}
  <label for="{{ $id }}" class="inline-flex items-center cursor-pointer select-none">
    <input type="hidden" name="{{ $name }}" value="0">
    <input id="{{ $id }}" type="checkbox" name="{{ $name }}" value="1"
           @checked(old($name, $checked)) class="peer sr-only">
    <span class="relative inline-block w-10 h-6 rounded-full bg-gray-300 peer-checked:bg-orange-500 transition">
      <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow
                   transition peer-checked:translate-x-4"></span>
    </span>
  </label>
</div>
