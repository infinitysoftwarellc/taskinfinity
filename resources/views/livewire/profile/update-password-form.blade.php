{{-- Password update card used inside the profile page. --}}
<div class="space-y-6">
    <header class="space-y-1">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Update Password') }}</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit.prevent="updatePassword" class="space-y-6">
        <div>
            <label for="profile_current_password" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                {{ __('Current Password') }}
            </label>
            <input
                wire:model="current_password"
                id="profile_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
            />
            @error('current_password')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile_password" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                {{ __('New Password') }}
            </label>
            <input
                wire:model="password"
                id="profile_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
            />
            @error('password')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="profile_password_confirmation" class="block text-sm font-medium text-zinc-800 dark:text-zinc-200">
                {{ __('Confirm Password') }}
            </label>
            <input
                wire:model="password_confirmation"
                id="profile_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
            />
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
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
