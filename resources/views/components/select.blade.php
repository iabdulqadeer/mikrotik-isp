@props(['label' => null, 'name' => null, 'id' => null, 'options' => [], 'value' => null, 'required' => false])

@php
  $id = $id ?: $name;
  $current = old($name, $value);
@endphp

<div>
  @if($label)
    <label for="{{ $id }}" class="block text-sm mb-1">{{ $label }}</label>
  @endif

  <select
    id="{{ $id }}"
    name="{{ $name }}"
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500']) }}
  >
    @foreach($options as $opt)
      @php
        $optVal = is_array($opt) ? $opt['value'] : $opt;
        $optLabel = is_array($opt) ? $opt['label'] : $opt;
      @endphp
      <option value="{{ $optVal }}" @selected($current == $optVal)>{{ $optLabel }}</option>
    @endforeach
  </select>

  @error($name)
    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
  @enderror
</div>
