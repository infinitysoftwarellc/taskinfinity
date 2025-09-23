@props(['name', 'maxWidth' => '2xl', 'focusable' => false])

@php
$maxWidthClasses = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail.name === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail.name === '{{ $name }}') show = false"
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    style="display: none;"
>
    {{-- Overlay --}}
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="show" x-transition></div>

    {{-- Conteúdo --}}
    <div
        class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all w-full {{ $maxWidthClasses }} mx-auto z-50"
        x-show="show"
        x-transition
        @if ($focusable) tabindex="-1" @endif
    >
        {{ $slot }}
    </div>
</div>
