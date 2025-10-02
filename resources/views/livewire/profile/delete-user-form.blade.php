{{-- Delete account card used on the profile page. --}}
<div class="space-y-6">
    <header class="space-y-1">
        <h2 class="text-lg font-semibold text-red-600 dark:text-red-400">{{ __('Delete Account') }}</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        wire:click="confirmDeletion"
        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
    >
        {{ __('Delete Account') }}
    </button>

    @if ($confirmingDeletion)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 px-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-900">
                <form wire:submit.prevent="deleteUser" class="space-y-6">
                    <header class="space-y-2">
                        <h3 class="text-lg font-semibold">{{ __('Are you sure you want to delete your account?') }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>
                    </header>

                    <div>
                        <label for="profile_delete_password" class="block text-sm font-medium">
                            {{ __('Password') }}
                        </label>
                        <input
                            wire:model.defer="password"
                            id="profile_delete_password"
                            name="password"
                            type="password"
                            class="mt-1 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-zinc-700 dark:bg-zinc-800"
                        />
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="cancelDeletion"
                            class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800"
                        >
                            {{ __('Cancel') }}
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500"
                        >
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
