{{-- Password settings page rendered by Livewire. --}}
<div class="flex min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    @include('app.shared.navigation')

    <main class="flex-1 px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto w-full max-w-3xl space-y-8">
            <header class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight">{{ __('Settings') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Update the security preferences for your account') }}
                </p>
                <div class="h-px w-full bg-zinc-200 dark:bg-zinc-700"></div>
            </header>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                <div class="mb-6 space-y-1">
                    <h2 class="text-xl font-semibold">{{ __('Update password') }}</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Ensure your account is using a long, random password to stay secure') }}
                    </p>
                </div>

                <form wire:submit.prevent="updatePassword" class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium">
                            {{ __('Current password') }}
                        </label>
                        <input
                            wire:model="current_password"
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                        />
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="password" class="block text-sm font-medium">
                                {{ __('New password') }}
                            </label>
                            <input
                                wire:model="password"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                            @error('password')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium">
                                {{ __('Confirm password') }}
                            </label>
                            <input
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                            />
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                        >
                            {{ __('Save changes') }}
                        </button>

                        @if ($flashMessage)
                            <p class="text-sm text-green-600 dark:text-green-400">{{ $flashMessage }}</p>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
