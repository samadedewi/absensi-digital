@props(['href' => '#', 'active' => false, 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl bg-white/10 text-white shadow-inner transition-all'
            : 'flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-indigo-100 hover:bg-white/5 hover:text-white transition-all';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
        </svg>
    @endif
    <span>{{ $slot }}</span>
</a>