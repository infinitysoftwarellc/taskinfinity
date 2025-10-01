{{-- This Blade view renders the profile interface. --}}
<x-app-layout>
    <div class="flex min-h-screen bg-zinc-100">
        @include('app.shared.navigation')

        <div class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200">
                    <div class="max-w-xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200">
                    <div class="max-w-xl">
                        <livewire:profile.update-password-form />
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-200">
                    <div class="max-w-xl">
                        <livewire:profile.delete-user-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
