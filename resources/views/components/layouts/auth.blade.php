{{-- This Blade view renders the components layouts auth interface. --}}
<x-layouts.auth.simple :title="$title ?? null">
    {{ $slot }}
</x-layouts.auth.simple>
