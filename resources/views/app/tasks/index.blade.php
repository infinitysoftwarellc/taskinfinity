{{-- resources/views/webapp/tasks/index.blade.php --}}
<x-app-layout>
    <livewire:tasks.board :list-id="$listId ?? null" />
</x-app-layout>
