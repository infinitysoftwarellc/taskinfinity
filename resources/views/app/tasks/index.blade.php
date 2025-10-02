{{-- This Blade view renders the app tasks index interface. --}}
{{-- resources/views/app/tasks/index.blade.php --}}
<x-app-layout>
    <livewire:tasks.board
        :list-id="$listId ?? null"
        :shortcut="$shortcut ?? null"
        :initial-mission-id="$focusMissionId ?? null"
    />
</x-app-layout>
