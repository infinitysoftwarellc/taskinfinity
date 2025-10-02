{{-- Recovery codes section displayed when 2FA is enabled. --}}
<div class="rounded-xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-900" x-data="{ open: false, copied: false }">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Recovery Codes') }}</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Store these codes in a safe place. Each code can be used once if you lose access to your authenticator app.') }}
            </p>
        </div>

        <div class="flex gap-2">
            <button
                type="button"
                x-show="!open"
                x-on:click="open = true"
                class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
                {{ __('View codes') }}
            </button>
            <button
                type="button"
                x-show="open"
                x-on:click="open = false"
                class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
            >
                {{ __('Hide codes') }}
            </button>
            <button
                type="button"
                x-show="open"
                wire:click="regenerateRecoveryCodes"
                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                {{ __('Regenerate') }}
            </button>
        </div>
    </div>

    <div x-show="open" x-transition class="mt-4 space-y-3">
        @error('recoveryCodes')
            <p class="rounded-lg bg-red-100 px-3 py-2 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-200">{{ $message }}</p>
        @enderror

        @if (filled($recoveryCodes))
            <div class="grid gap-2 rounded-lg bg-white p-4 font-mono text-sm tracking-widest shadow-sm dark:bg-zinc-800">
                @foreach ($recoveryCodes as $code)
                    <span class="select-all">{{ $code }}</span>
                @endforeach
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Each code can be used once. Generate a new set after using one to keep your account secure.') }}
            </p>
        @else
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('No recovery codes are available yet. Enable two-factor authentication to generate codes.') }}
            </p>
        @endif
    </div>
</div>
