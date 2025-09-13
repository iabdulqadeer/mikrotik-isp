@props([
  'label' => null,
  'name' => null,
  'id' => null,
  'rows' => 4,
  'value' => null,
  'placeholder' => null,
  'required' => false,
])

@php
  $id = $id ?: $name;

  // Priority: old() > explicit value prop > slot content
  $current = old($name, $value);
  if(!$current && isset($slot) && trim($slot) !== '') {
    $current = $slot;
  }
@endphp

<div>
  @if($label)
    <label for="{{ $id }}" class="block text-sm mb-1">{{ $label }}</label>
  @endif

  <textarea
    id="{{ $id }}"
    name="{{ $name }}"
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500']) }}
  >{{ $current }}</textarea>

  @error($name)
    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
  @enderror
</div>
