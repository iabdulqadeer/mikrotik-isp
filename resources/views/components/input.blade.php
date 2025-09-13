{{-- resources/views/components/input.blade.php --}}
@props([
  'label' => null,
  'name' => null,
  'id' => null,
  'type' => 'text',
  'value' => null,
  'placeholder' => null,
  'required' => false,
  'disabled' => false,
  'step' => null,
  'min' => null,
  'max' => null,
])

@php
  $id = $id ?: $name;
  // old() fallback so validation errors keep input filled
  $current = old($name, $value);
@endphp

<div>
  @if($label)
    <label for="{{ $id }}" class="block text-sm mb-1">{{ $label }}</label>
  @endif

  <input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    @if(!is_null($placeholder)) placeholder="{{ $placeholder }}" @endif
    @if(!is_null($step)) step="{{ $step }}" @endif
    @if(!is_null($min)) min="{{ $min }}" @endif
    @if(!is_null($max)) max="{{ $max }}" @endif
    @if($required) required @endif
    @if($disabled) disabled @endif
    value="{{ $current }}"
    {{ $attributes->merge(['class' => 'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500']) }}
  />

  @error($name)
    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
  @enderror
</div>
