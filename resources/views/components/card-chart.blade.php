{{-- resources/views/components/card-chart.blade.php --}}
@props(['title' => '', 'filter' => '', 'canvasId' => 'chart'])
<div class="bg-white rounded-2xl border shadow-sm">
  <div class="flex items-center justify-between px-4 py-3 border-b">
    <div class="font-medium">{{ $title }}</div>
    @if($filter)
      <div class="text-[12px] text-gray-500">{{ $filter }}</div>
    @endif
  </div>
  <div class="p-4">
    <canvas id="{{ $canvasId }}" class="w-full h-64"></canvas>
  </div>
</div>
