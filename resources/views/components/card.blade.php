<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="font-semibold text-gray-800">{{ $title }}</div>
        <div>{{ $actions ?? '' }}</div>
    </div>
    {{ $slot }}
</div>