{{-- Navigation rail displayed on authenticated pages. --}}
@php
    $userName = auth()->user()?->name ?? 'User';
    $initials = mb_strtoupper(mb_substr($userName, 0, 2));
@endphp

<aside class="hidden w-16 shrink-0 flex-col items-center gap-3 border-r border-zinc-200 bg-white px-2 py-6 text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300 lg:flex">
    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-white">
        <span class="text-sm font-semibold">{{ $initials }}</span>
    </div>

<nav class="flex flex-col items-center gap-2 text-lg" aria-label="Primary">
    @foreach ([
        ['icon' => 'fa-list-check', 'label' => __('All tasks')],
        ['icon' => 'fa-sun', 'label' => __('Today')],
        ['icon' => 'fa-calendar-days', 'label' => __('Next 7 days')],
        ['icon' => 'fa-inbox', 'label' => __('Inbox')],
        ['icon' => 'fa-chart-pie', 'label' => __('Summary')],
    ] as $item)
        <button
            type="button"
            class="flex h-10 w-10 items-center justify-center rounded-lg text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
            title="{{ $item['label'] }}"
        >
            <i class="fa-solid {{ $item['icon'] }}" aria-hidden="true"></i>
            <span class="sr-only">{{ $item['label'] }}</span>
        </button>
    @endforeach
</nav>

<div class="mt-auto flex flex-col items-center gap-2">
    <a
        href="{{ route('app.settings') }}"
        wire:navigate
        class="flex h-10 w-10 items-center justify-center rounded-lg text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
        title="{{ __('Settings') }}"
    >
        <i class="fa-solid fa-gear" aria-hidden="true"></i>
        <span class="sr-only">{{ __('Settings') }}</span>
    </a>
    <form wire:submit.prevent="logout" class="w-full">
        @csrf
        <button
            type="submit"
            class="flex h-10 w-full items-center justify-center rounded-lg text-red-500 transition hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-900/40"
            title="{{ __('Logout') }}"
        >
            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
            <span class="sr-only">{{ __('Logout') }}</span>
        </button>
    </form>
</div>
</aside>
