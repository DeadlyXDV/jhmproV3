@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}" wire:navigate
    @class([
        'flex items-center gap-3.5 py-2.5 px-4 rounded-xl text-sm group relative',
        'bg-[#E11D22] text-white font-bold shadow-lg shadow-red-600/20' => $active,
        'text-gray-400 hover:bg-white/5 hover:text-white font-medium' => ! $active,
    ])
    :class="collapsed ? 'px-0 justify-center' : 'px-4'"
    :title="collapsed ? '{{ $slot }}' : ''"
>
    <x-dynamic-component :component="'heroicon-' . $icon"
        @class([
            'w-5 h-5 flex-shrink-0',
            'text-white' => $active,
            'text-gray-500 group-hover:text-white' => ! $active,
        ]) />
    <span class="truncate w-auto opacity-100 transition-none"
        :class="collapsed ? 'w-0 opacity-0' : 'w-auto opacity-100'">
        {{ $slot }}
    </span>
</a>
