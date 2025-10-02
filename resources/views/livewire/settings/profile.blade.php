{{-- Profile settings page rendered by Livewire. --}}
<div class="flex min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    @include('app.shared.navigation')

    <main class="flex-1 px-4 py-8 sm:px-6 lg:px-10">
        <div class="mx-auto w-full max-w-3xl space-y-8">
            <header class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight">{{ __('Settings') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Manage your profile and account settings') }}
                </p>
                <div class="h-px w-full bg-zinc-200 dark:bg-zinc-700"></div>
            </header>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                <div class="mb-6 space-y-1">
                    <h2 class="text-xl font-semibold">{{ __('Profile') }}</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Update your name and email address') }}
                    </p>
                </div>

                <form wire:submit.prevent="updateProfileInformation" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium">
                            {{ __('Name') }}
                        </label>
                        <input
                            wire:model="name"
                            id="name"
                            name="name"
                            type="text"
                            autocomplete="name"
                            required
                            class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                        />
                        @error('name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium">
                            {{ __('Email') }}
                        </label>
                        <input
                            wire:model="email"
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                        />
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                            <div class="mt-4 space-y-2 text-sm">
                                <p>{{ __('Your email address is unverified.') }}</p>
                                <button
                                    type="button"
                                    wire:click.prevent="resendVerificationNotification"
                                    class="font-medium text-indigo-600 underline transition hover:text-indigo-500"
                                >
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="text-green-600 dark:text-green-400">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                @endif
                            </div>
                        @endif
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

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                <livewire:settings.delete-user-form />
            </section>
        </div>
    </main>
</div>
