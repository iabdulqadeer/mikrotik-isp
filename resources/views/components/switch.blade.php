@props(['label' => null, 'name' => null, 'checked' => false, 'id' => null])

@php
  $id = $id ?: $name;
@endphp

<label for="{{ $id }}" class="inline-flex items-center gap-2 cursor-pointer">
  <input type="hidden" name="{{ $name }}" value="0">
  <input id="{{ $id }}" type="checkbox" name="{{ $name }}" value="1"
         @checked(old($name, $checked))
         class="peer sr-only">

  <span class="w-10 h-6 rounded-full bg-gray-300 peer-checked:bg-indigo-600 relative transition">
    <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition peer-checked:translate-x-4"></span>
  </span>

  @if($label)
    <span class="text-sm text-gray-700">{{ $label }}</span>
  @endif
</label>
