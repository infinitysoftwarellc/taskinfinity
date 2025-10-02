{{-- This Blade view renders the layouts app interface. --}}
<x-layouts.app.sidebar :title="$title ?? null">
    {{ $slot }}
</x-layouts.app.sidebar>
