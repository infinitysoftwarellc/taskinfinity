<x-app-layout>
    @php
        $menuLinks = [
            [
                'label' => 'Tasks',
                'route' => route('tasks.index'),
                'active' => request()->routeIs('tasks.*'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5M3.75 9.75h16.5M3.75 14.25h16.5M3.75 18.75h16.5" />',
                'stroke' => true,
            ],
            [
                'label' => 'Pomodoro',
                'route' => route('pomodoro'),
                'active' => request()->routeIs('pomodoro'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5c-4.142 0-7.5 3.134-7.5 7s3.358 7 7.5 7 7.5-3.134 7.5-7" />',
                'stroke' => true,
            ],
            [
                'label' => 'Habits',
                'route' => route('habits'),
                'active' => request()->routeIs('habits'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75v2.25m10.5-2.25v2.25M4.5 9.75h15M6 7.5h12a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V9A1.5 1.5 0 0 1 6 7.5z" />',
                'stroke' => true,
            ],
            [
                'label' => 'Profile',
                'route' => route('profile'),
                'active' => request()->routeIs('profile'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.5 19.125a8.25 8.25 0 0 1 15 0" />',
                'stroke' => true,
            ],
        ];
    @endphp

    <div class="flex min-h-screen bg-gray-950 text-gray-100">
        <aside class="hidden w-20 shrink-0 flex-col items-center border-r border-white/10 bg-black/30 py-6 md:flex">
            <a href="{{ route('tasks.index') }}" class="mb-8 flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500 text-white transition hover:bg-indigo-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75" />
                </svg>
            </a>
            <nav class="flex flex-1 flex-col items-center gap-4">
                @foreach ($menuLinks as $link)
                    <a
                        href="{{ $link['route'] }}"
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70 transition hover:text-white {{ ($link['active'] ?? false) ? 'border-indigo-400 text-white' : '' }}"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="{{ ($link['stroke'] ?? false) ? 'none' : 'currentColor' }}" stroke="currentColor" stroke-width="1.5">
                            {!! $link['icon'] !!}
                        </svg>
                    </a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/70 transition hover:text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3m0 0-3 3m3-3H3" />
                    </svg>
                </button>
            </form>
        </aside>

        <div class="flex flex-1 flex-col gap-6 p-4 sm:p-6">
            <div class="md:hidden">
                <nav class="mb-4 flex items-center gap-3 overflow-x-auto rounded-2xl border border-white/10 bg-black/30 p-3 text-sm text-white/70">
                    @foreach ($menuLinks as $link)
                        <a
                            href="{{ $link['route'] }}"
                            class="flex items-center gap-2 rounded-xl px-3 py-2 transition hover:text-white {{ ($link['active'] ?? false) ? 'bg-indigo-500 text-white' : 'bg-white/5' }}"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ ($link['stroke'] ?? false) ? 'none' : 'currentColor' }}" stroke="currentColor" stroke-width="1.5">
                                {!! $link['icon'] !!}
                            </svg>
                            <span class="whitespace-nowrap">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-600/40 via-purple-600/30 to-indigo-500/20 p-8 shadow-2xl shadow-indigo-500/20">
                <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-indigo-400/30 blur-3xl"></div>
                <div class="absolute -bottom-32 left-16 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl"></div>
                <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-200/80">{{ __('Central da conta') }}</p>
                        <h2 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">{{ __('Seu perfil') }}</h2>
                        <p class="mt-3 text-sm text-indigo-100/90">
                            {{ __('Personalize suas informações, mantenha sua conta protegida e controle sua presença no TaskInfinity em um só lugar.') }}
                        </p>
                    </div>
                    <div class="w-full max-w-xs rounded-3xl border border-white/20 bg-white/10 p-6 text-sm text-indigo-100 shadow-xl shadow-black/20">
                        <div class="flex items-center gap-3">
                            <span class="relative flex h-12 w-12 shrink-0 overflow-hidden rounded-2xl border border-white/20 bg-white/10 text-lg font-semibold text-white">
                                <span class="flex h-full w-full items-center justify-center">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>
                            <div>
                                <p class="text-base font-semibold text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-indigo-100/80">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <dl class="mt-4 space-y-2 text-xs uppercase tracking-widest text-indigo-200/80">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                                <span>{{ __('Conta ativa') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-white/60"></span>
                                <span>{{ __('Último acesso: agora mesmo') }}</span>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-[320px_1fr]">
                <nav class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-6 shadow-xl shadow-black/20 backdrop-blur">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-white/70">{{ __('Ajustes rápidos') }}</h3>
                    <p class="mt-2 text-sm text-white/50">{{ __('Acesse cada seção para atualizar detalhes e segurança da sua conta.') }}</p>
                    <ul class="mt-6 space-y-3">
                        <li>
                            <a href="#profile-info" class="group flex items-center gap-3 rounded-2xl border border-white/5 bg-white/5 px-4 py-3 text-sm text-white/70 transition hover:border-indigo-300/60 hover:bg-indigo-500/20 hover:text-white">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-200 transition group-hover:bg-indigo-500 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                        <path d="M12 12.75a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z" />
                                        <path d="M3.75 20.25a8.25 8.25 0 0 1 16.5 0 .75.75 0 0 1-.75.75H4.5a.75.75 0 0 1-.75-.75Z" />
                                    </svg>
                                </span>
                                {{ __('Informações básicas') }}
                            </a>
                        </li>
                        <li>
                            <a href="#profile-security" class="group flex items-center gap-3 rounded-2xl border border-white/5 bg-white/5 px-4 py-3 text-sm text-white/70 transition hover:border-purple-300/60 hover:bg-purple-500/20 hover:text-white">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-purple-500/20 text-purple-200 transition group-hover:bg-purple-500 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                        <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v1.076c0 .238-.134.438-.293.597C5.063 9.85 4.5 10.707 4.5 11.625v5.625A2.25 2.25 0 0 0 6.75 19.5h10.5a2.25 2.25 0 0 0 2.25-2.25v-5.625c0-.918-.563-1.775-1.957-3.202-.16-.159-.293-.36-.293-.597V6.75A5.25 5.25 0 0 0 12 1.5Zm0 5.25c.621 0 1.125.504 1.125 1.125v1.5a1.125 1.125 0 1 1-2.25 0v-1.5c0-.621.504-1.125 1.125-1.125ZM12 12.75a1.875 1.875 0 1 0 0 3.75 1.875 1.875 0 0 0 0-3.75Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                {{ __('Segurança e senha') }}
                            </a>
                        </li>
                        <li>
                            <a href="#profile-danger" class="group flex items-center gap-3 rounded-2xl border border-white/5 bg-white/5 px-4 py-3 text-sm text-white/70 transition hover:border-red-300/60 hover:bg-red-500/20 hover:text-white">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-red-500/20 text-red-200 transition group-hover:bg-red-500 group-hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                        <path fill-rule="evenodd" d="M9.401 1.592a1.875 1.875 0 0 1 3.198 0l8.862 14.551c.823 1.35-.158 3.084-1.6 3.084H2.14c-1.441 0-2.423-1.734-1.6-3.084L9.4 1.592ZM12 8.25a.75.75 0 0 0-.75.75v3.75a.75.75 0 0 0 1.5 0V9a.75.75 0 0 0-.75-.75Zm0 8.25a.875.875 0 1 0 0-1.75.875.875 0 0 0 0 1.75Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                {{ __('Zona de risco') }}
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="space-y-6">
                    <section id="profile-info" class="overflow-hidden rounded-3xl border border-white/10 bg-white/95 text-gray-900 shadow-2xl shadow-black/10 backdrop-blur dark:border-white/5 dark:bg-slate-900/80 dark:text-gray-100">
                        <div class="border-b border-black/5 bg-gradient-to-r from-white via-white to-indigo-50/70 px-6 py-6 dark:border-white/5 dark:from-slate-900 dark:via-slate-900 dark:to-indigo-950/30">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Informações básicas') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Atualize nome, foto e dados de contato para manter sua conta alinhada.') }}</p>
                        </div>
                        <div class="p-6 sm:p-8">
                            <div class="max-w-2xl">
                                <livewire:profile.update-profile-information-form />
                            </div>
                        </div>
                    </section>

                    <section id="profile-security" class="overflow-hidden rounded-3xl border border-white/10 bg-white/95 text-gray-900 shadow-2xl shadow-black/10 backdrop-blur dark:border-white/5 dark:bg-slate-900/80 dark:text-gray-100">
                        <div class="border-b border-black/5 bg-gradient-to-r from-white via-white to-purple-50/70 px-6 py-6 dark:border-white/5 dark:from-slate-900 dark:via-slate-900 dark:to-purple-950/30">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Segurança e senha') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Reforce sua proteção redefinindo uma senha única e segura.') }}</p>
                        </div>
                        <div class="p-6 sm:p-8">
                            <div class="max-w-2xl">
                                <livewire:profile.update-password-form />
                            </div>
                        </div>
                    </section>

                    <section id="profile-danger" class="overflow-hidden rounded-3xl border border-white/10 bg-white/95 text-gray-900 shadow-2xl shadow-black/10 backdrop-blur dark:border-white/5 dark:bg-slate-900/80 dark:text-gray-100">
                        <div class="border-b border-black/5 bg-gradient-to-r from-white via-white to-rose-50/70 px-6 py-6 dark:border-white/5 dark:from-slate-900 dark:via-slate-900 dark:to-rose-950/30">
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
</x-app-layout>
