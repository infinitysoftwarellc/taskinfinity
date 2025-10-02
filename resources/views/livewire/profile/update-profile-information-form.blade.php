{{-- Profile information card used inside the profile page. --}}
<div class="space-y-6">
    <header class="space-y-1">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Profile Information') }}</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit.prevent="updateProfileInformation" class="space-y-6">
        <div>
            <label for="profile_name" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                {{ __('Name') }}
            </label>
            <input
                wire:model="name"
                id="profile_name"
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
            <label for="profile_email" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                {{ __('Email') }}
            </label>
            <input
                wire:model="email"
                id="profile_email"
                name="email"
                type="email"
                autocomplete="username"
                required
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
            />
            @error('email')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3 space-y-2 text-sm">
                    <p>{{ __('Your email address is unverified.') }}</p>
                    <button
                        type="button"
                        wire:click.prevent="sendVerification"
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
                {{ __('Save') }}
            </button>

            @if ($flashMessage)
                <p class="text-sm text-green-600 dark:text-green-400">{{ $flashMessage }}</p>
            @endif
        </div>
    </form>
</div>
