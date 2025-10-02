{{-- Tasks board page. --}}
@extends('layouts.app')

@section('title', __('Tasks'))

@section('content')
    <div class="flex min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
        @include('app.shared.navigation')

        <main class="flex-1 overflow-hidden">
            <livewire:tasks.board
                :list-id="$listId ?? null"
                :shortcut="$shortcut ?? null"
                :initial-mission-id="$focusMissionId ?? null"
            />
        </main>
    </div>
@endsection
