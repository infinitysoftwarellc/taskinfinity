{{-- resources/views/app/tasks/index.blade.php --}}
<x-app-layout>
    <div class="flex min-h-screen bg-zinc-100 dark:bg-zinc-950">
        @include('app.shared.navigation')

        <div class="flex-1 overflow-hidden px-4 py-6 sm:px-6 lg:px-8">
            <livewire:tasks.board :list-id="$listId ?? null" :shortcut="$shortcut ?? null" />
        </div>
    </div>
</x-app-layout>
