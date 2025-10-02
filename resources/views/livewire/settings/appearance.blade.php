{{-- Appearance settings page rendered by Livewire. --}}
<div class="flex min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    @include('app.shared.navigation')

    <main class="flex-1 px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto w-full max-w-3xl space-y-8">
            <header class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight">{{ __('Settings') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Update the appearance settings for your account') }}
                </p>
                <div class="h-px w-full bg-zinc-200 dark:bg-zinc-700"></div>
            </header>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                <div class="mb-6 space-y-1">
                    <h2 class="text-xl font-semibold">{{ __('Appearance') }}</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Choose how TaskInfinity looks on this device') }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3" x-data="{ theme: document.documentElement.dataset.theme ?? 'system' }">
                    @foreach ([
                        ['value' => 'light', 'label' => __('Light'), 'description' => __('Bright and clean')],
                        ['value' => 'dark', 'label' => __('Dark'), 'description' => __('Low-light friendly')],
                        ['value' => 'system', 'label' => __('System'), 'description' => __('Follow OS preference')],
                    ] as $option)
                        <label class="flex cursor-pointer flex-col rounded-xl border border-transparent bg-zinc-100 p-4 text-sm transition hover:border-indigo-400 hover:bg-zinc-50 dark:bg-zinc-800 dark:hover:border-indigo-500" :class="theme === '{{ $option['value'] }}' ? 'ring-2 ring-indigo-500 border-indigo-500 dark:ring-indigo-400' : ''">
                            <input
                                type="radio"
                                name="appearance"
                                value="{{ $option['value'] }}"
                                class="sr-only"
                                x-model="theme"
                            />
                            <span class="font-semibold">{{ $option['label'] }}</span>
                            <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $option['description'] }}</span>
                        </label>
                    @endforeach
                </div>

                <p class="mt-6 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ __('Themes are applied instantly and remembered for your next visit.') }}
                </p>
            </section>
        </div>
    </main>
</div>
