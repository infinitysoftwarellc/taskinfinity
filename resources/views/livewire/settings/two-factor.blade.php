{{-- Two-factor authentication settings page rendered by Livewire. --}}
<div class="flex min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    @include('app.shared.navigation')

    <main class="flex-1 px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto w-full max-w-3xl space-y-8" wire:cloak>
            <header class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight">{{ __('Settings') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Manage your two-factor authentication settings') }}
                </p>
                <div class="h-px w-full bg-zinc-200 dark:bg-zinc-700"></div>
            </header>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                <div class="mb-6 space-y-1">
                    <h2 class="text-xl font-semibold">{{ __('Two Factor Authentication') }}</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Protect your account by requiring a code when logging in on a new device.') }}
                    </p>
                </div>

                @error('setupData')
                    <p class="mb-4 rounded-lg bg-red-100 px-3 py-2 text-sm text-red-700 dark:bg-red-900/40 dark:text-red-200">{{ $message }}</p>
                @enderror

                @if ($twoFactorEnabled)
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                {{ __('Enabled') }}
                            </span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Two-factor authentication is currently protecting your account.') }}
                            </span>
                        </div>

                        <livewire:settings.two-factor.recovery-codes :requires-confirmation="$requiresConfirmation" />

                        <div>
                            <button
                                type="button"
                                wire:click="disable"
                                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                            >
                                {{ __('Disable 2FA') }}
                            </button>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                {{ __('Disabled') }}
                            </span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Enable two-factor authentication to add an extra layer of security to your account.') }}
                            </span>
                        </div>

                        <button
                            type="button"
                            wire:click="enable"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                        >
                            {{ __('Enable 2FA') }}
                        </button>
                    </div>
                @endif
            </section>
        </div>
    </main>

    @if ($showModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="space-y-4">
                    <header class="space-y-1 text-center">
                        <h3 class="text-lg font-semibold">{{ $this->modalConfig['title'] }}</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $this->modalConfig['description'] }}</p>
                    </header>

                    @if ($qrCodeSvg)
                        <div class="flex justify-center">
                            <div class="rounded-xl border border-dashed border-zinc-300 p-4 dark:border-zinc-700">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    @endif

                    @if ($manualSetupKey)
                        <div class="rounded-lg bg-zinc-100 p-3 text-center font-mono text-sm tracking-wider dark:bg-zinc-800">
                            {{ $manualSetupKey }}
                        </div>
                    @endif

                    @if ($showVerificationStep)
                        <form wire:submit.prevent="confirmTwoFactor" class="space-y-4">
                            <label class="block text-sm font-medium" for="two_factor_code">
                                {{ __('Verification code') }}
                            </label>
                            <input
                                id="two_factor_code"
                                wire:model.defer="code"
                                type="text"
                                inputmode="numeric"
                                maxlength="6"
                                class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-center tracking-[0.5em] focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-800"
                            />
                            @error('code')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror

                            <div class="flex justify-end gap-2">
                                <button
                                    type="button"
                                    wire:click="resetVerification"
                                    class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
                                >
                                    {{ __('Back') }}
                                </button>
                                <button
                                    type="submit"
                                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    {{ __('Confirm') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="flex justify-end">
                            <button
                                type="button"
                                wire:click="showVerificationIfNecessary"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                {{ $this->modalConfig['buttonText'] }}
                            </button>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="text-sm text-zinc-500 underline transition hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                        >
                            {{ __('Close window') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
