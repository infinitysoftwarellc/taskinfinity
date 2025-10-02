@pure

@props([
    'icon' => null,
    'name' => null,
])

@php
    $icon = $name ?? $icon;
@endphp

@if (view()->exists('flux.icon.' . $icon))
    <flux:delegate-component :component="'icon.' . $icon">{{ $slot }}</flux:delegate-component>
@else
    {{-- Generic fallback icon when Flux icon is missing --}}
    <svg {{ $attributes->class('h-5 w-5 text-slate-500') }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
        <path d="m9 12 2 2 4-4" />
    </svg>
@endif
