@props(['href', 'icon', 'active' => false])

<a href="{{ $href }}"
    @class([
        'flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors',
        'bg-red-600 text-white font-medium' => $active,
        'text-gray-400 hover:bg-white/10 hover:text-white' => ! $active,
    ])>
    <x-dynamic-component :component="'heroicon-' . $icon" class="w-4 h-4 flex-shrink-0" />
    {{ $slot }}
</a>
