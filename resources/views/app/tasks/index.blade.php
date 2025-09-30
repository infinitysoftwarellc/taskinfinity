{{-- resources/views/app/tasks/index.blade.php --}}
<x-app-layout>
    <livewire:tasks.board :list-id="$listId ?? null" :shortcut="$shortcut ?? null" />
</x-app-layout>
