<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="text-3xl font-semibold text-gray-900 dark:text-white">
                {{ __('Seu perfil') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                {{ __('Personalize suas informações, mantenha sua conta protegida e controle sua presença no TaskInfinity em um só lugar.') }}
            </p>
        </div>
    </x-slot>

    <div class="relative isolate">
        <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-br from-indigo-100 via-white to-purple-100 dark:from-slate-900 dark:via-slate-950 dark:to-indigo-950"></div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-[260px_1fr] xl:grid-cols-[280px_1fr]">
                    <aside class="lg:sticky lg:top-24 self-start">
                        <div class="overflow-hidden rounded-3xl border border-white/60 bg-white/80 shadow-xl shadow-indigo-200/40 backdrop-blur-sm transition-all dark:border-white/5 dark:bg-slate-900/80 dark:shadow-none">
                            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-600 p-6">
                                <h3 class="text-lg font-semibold text-white">{{ __('Central da conta') }}</h3>
                                <p class="mt-1 text-sm text-indigo-100">{{ __('Acesse rapidamente cada ajuste do seu perfil.') }}</p>
                            </div>
                            <nav class="p-4">
                                <ul class="space-y-2">
                                    <li>
                                        <a href="#profile-info" class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-indigo-50 hover:text-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 dark:text-gray-300 dark:hover:bg-slate-800/80 dark:hover:text-white">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white dark:bg-indigo-500/20 dark:text-indigo-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                    <path d="M12 12.75a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z" />
                                                    <path d="M3.75 20.25a8.25 8.25 0 0 1 16.5 0 .75.75 0 0 1-.75.75H4.5a.75.75 0 0 1-.75-.75Z" />
                                                </svg>
                                            </span>
                                            {{ __('Informações básicas') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#profile-security" class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-indigo-50 hover:text-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 dark:text-gray-300 dark:hover:bg-slate-800/80 dark:hover:text-white">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 text-purple-600 transition group-hover:bg-purple-600 group-hover:text-white dark:bg-purple-500/20 dark:text-purple-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v1.076c0 .238-.134.438-.293.597C5.063 9.85 4.5 10.707 4.5 11.625v5.625A2.25 2.25 0 0 0 6.75 19.5h10.5a2.25 2.25 0 0 0 2.25-2.25v-5.625c0-.918-.563-1.775-1.957-3.202-.16-.159-.293-.36-.293-.597V6.75A5.25 5.25 0 0 0 12 1.5Zm0 5.25c.621 0 1.125.504 1.125 1.125v1.5a1.125 1.125 0 1 1-2.25 0v-1.5c0-.621.504-1.125 1.125-1.125ZM12 12.75a1.875 1.875 0 1 0 0 3.75 1.875 1.875 0 0 0 0-3.75Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            {{ __('Segurança e senha') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#profile-danger" class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-red-50 hover:text-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-red-500 dark:text-gray-300 dark:hover:bg-slate-800/80 dark:hover:text-red-300">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-red-100 text-red-600 transition group-hover:bg-red-600 group-hover:text-white dark:bg-red-500/20 dark:text-red-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                                    <path fill-rule="evenodd" d="M9.401 1.592a1.875 1.875 0 0 1 3.198 0l8.862 14.551c.823 1.35-.158 3.084-1.6 3.084H2.14c-1.441 0-2.423-1.734-1.6-3.084L9.4 1.592ZM12 8.25a.75.75 0 0 0-.75.75v3.75a.75.75 0 0 0 1.5 0V9a.75.75 0 0 0-.75-.75Zm0 8.25a.875.875 0 1 0 0-1.75.875.875 0 0 0 0 1.75Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            {{ __('Zona de risco') }}
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </aside>

                    <div class="space-y-8">
                        <section id="profile-info" class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-xl shadow-indigo-200/40 backdrop-blur-sm transition-all dark:border-white/5 dark:bg-slate-900/80 dark:shadow-none">
                            <div class="border-b border-gray-100/60 bg-gradient-to-r from-white via-white to-indigo-50 px-6 py-6 dark:border-white/5 dark:from-slate-900 dark:via-slate-900 dark:to-indigo-950/40">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Informações básicas') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Atualize nome, foto e dados de contato para manter sua conta alinhada.') }}</p>
                            </div>
                            <div class="p-6 sm:p-8">
                                <div class="max-w-2xl">
                                    <livewire:profile.update-profile-information-form />
                                </div>
                            </div>
                        </section>

                        <section id="profile-security" class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-xl shadow-indigo-200/40 backdrop-blur-sm transition-all dark:border-white/5 dark:bg-slate-900/80 dark:shadow-none">
                            <div class="border-b border-gray-100/60 bg-gradient-to-r from-white via-white to-purple-50 px-6 py-6 dark:border-white/5 dark:from-slate-900 dark:via-slate-900 dark:to-purple-950/40">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Segurança e senha') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Reforce sua proteção redefinindo uma senha única e segura.') }}</p>
                            </div>
                            <div class="p-6 sm:p-8">
                                <div class="max-w-2xl">
                                    <livewire:profile.update-password-form />
                                </div>
                            </div>
                        </section>

                        <section id="profile-danger" class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-xl shadow-rose-200/40 backdrop-blur-sm transition-all dark:border-white/5 dark:bg-slate-900/80 dark:shadow-none">
                            <div class="border-b border-gray-100/60 bg-gradient-to-r from-white via-white to-rose-50 px-6 py-6 dark:border-white/5 dark:from-slate-900 dark:via-slate-900 dark:to-rose-950/40">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Zona de risco') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Exclua sua conta permanentemente caso não deseje mais utilizar o TaskInfinity.') }}</p>
                            </div>
                            <div class="p-6 sm:p-8">
                                <div class="max-w-2xl">
                                    <livewire:profile.delete-user-form />
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
