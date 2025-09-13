<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold
    @if($variant === 'success') bg-green-100 text-green-700 @endif
    @if($variant === 'warning') bg-orange-100 text-orange-700 @endif
    @if($variant === 'pending') bg-gray-100 text-gray-500 @endif
">
    {{ $label }}
</span>