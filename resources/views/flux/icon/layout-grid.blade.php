{{-- Ícone do Font Awesome --}}

@props([
    'variant' => 'outline',
])

@php
    $classes = Flux::classes('shrink-0 fa-solid fa-grip fa-fw')->add(
        match ($variant) {
            'outline', 'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
            default => '[:where(&)]:size-6',
        },
    );
@endphp

<i
    {{ $attributes->class($classes) }}
    aria-hidden="true"
    data-flux-icon
    data-slot="icon"
></i>
