{{-- Administrative dashboard displaying platform metrics. --}}
@extends('layouts.app')

@section('title', __('Admin Dashboard'))

@section('content')
    <div class="flex min-h-screen bg-zinc-100 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
        @include('app.shared.navigation')

        <main class="flex-1 px-4 py-12 sm:px-6 lg:px-10">
            <div class="mx-auto max-w-6xl space-y-10">
                <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight">{{ __('Admin Dashboard') }}</h1>
                        <p class="mt-2 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Acompanhe o resumo da plataforma, incluindo usuários cadastrados, tarefas e listas ativas.') }}
                        </p>
                    </div>
                </header>

                <section>
                    <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ __('Resumo Rápido') }}</h2>
                    <dl class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Usuários cadastrados') }}</dt>
                            <dd class="mt-3 text-3xl font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($statistics->usersCount) }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total de tarefas') }}</dt>
                            <dd class="mt-3 text-3xl font-semibold text-sky-600 dark:text-sky-400">
                                {{ number_format($statistics->missionsCount) }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total de listas') }}</dt>
                            <dd class="mt-3 text-3xl font-semibold text-amber-600 dark:text-amber-400">
                                {{ number_format($statistics->listsCount) }}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section>
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ __('Usuários cadastrados') }}</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ trans_choice('{0}Nenhum usuário|{1}1 usuário|[2,*]:count usuários', $statistics->usersCount, ['count' => $statistics->usersCount]) }}</p>
                    </div>

                    <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ __('Nome') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ __('E-mail') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ __('Tarefas') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ __('Listas') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ __('Cadastro') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/80">
                                        <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ $user->name }}
                                            @if ($user->isAdmin())
                                                <span class="ml-2 inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">{{ __('Admin') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-semibold text-sky-600 dark:text-sky-300">
                                            {{ number_format($user->missions_count) }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-semibold text-amber-600 dark:text-amber-300">
                                            {{ number_format($user->task_lists_count) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ __('Nenhum usuário cadastrado até o momento.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
@endsection
