@props(['title', 'name', 'enabled' => false, 'key' => null])

<div class="rounded-xl border p-4 space-y-3">
  <div class="flex items-center justify-between">
    <div class="text-lg font-semibold">{{ $title }}</div>
    <x-switch name="{{ $name }}_enabled" :checked="$enabled" />
  </div>

  <x-input label="API Key" name="{{ $name }}_key" :value="$key" placeholder="Enter {{ $title }} API key" />
</div>
