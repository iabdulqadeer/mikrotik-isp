<div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full grid place-items-center
            @if($accent === 'orange') bg-orange-100 text-orange-500 @endif
            @if($accent === 'indigo') bg-indigo-100 text-indigo-500 @endif
            @if($accent === 'violet') bg-violet-100 text-violet-500 @endif
            @if($accent === 'amber') bg-amber-100 text-amber-500 @endif
        ">
            {{ @$icon }}
        </div>
        <div>
            <div class="text-sm font-medium text-gray-500">{{ $title }}</div>
            <div class="text-2xl font-bold text-gray-800">{{ $value }}</div>
            <div class="text-xs text-gray-400">{{ $note }}</div>
        </div>
    </div>
</div>