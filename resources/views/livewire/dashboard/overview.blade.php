<div class="py-12">
    <div class="mx-auto max-w-6xl space-y-8 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-500">Área administrativa</p>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Bem-vindo, {{ $userName }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Você está autenticado como administrador. Use esta visão geral para acompanhar métricas rápidas do sistema.
                </p>
            </div>
            <div class="rounded-2xl border border-indigo-200/60 bg-indigo-50 px-5 py-3 text-sm text-indigo-900 shadow dark:border-indigo-500/40 dark:bg-indigo-950 dark:text-indigo-200">
                Último acesso<br>
                <span class="text-lg font-semibold">{{ $loginAt->translatedFormat('d \d\e F \d\e Y H:i') }}</span>
            </div>
        </header>

        <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuários ativos</h2>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Hoje</span>
                </div>
                <p class="mt-4 text-4xl font-bold text-gray-900 dark:text-white">128</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Número fictício apenas para demonstração do layout.</p>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Receita mensal</h2>
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">R$</span>
                </div>
                <p class="mt-4 text-4xl font-bold text-gray-900 dark:text-white">R$ 9.420</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Simulação de receita mensal recorrente.</p>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tickets abertos</h2>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">Suporte</span>
                </div>
                <p class="mt-4 text-4xl font-bold text-gray-900 dark:text-white">5</p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Indicador ilustrativo dos chamados aguardando resposta.</p>
            </article>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Atividades recentes</h2>
                <ul class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex items-center justify-between rounded-2xl bg-gray-50 px-4 py-3 dark:bg-white/5">
                        <span>Nova assinatura criada</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Há 2 horas</span>
                    </li>
                    <li class="flex items-center justify-between rounded-2xl bg-gray-50 px-4 py-3 dark:bg-white/5">
                        <span>Plano Pro atualizado</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Há 5 horas</span>
                    </li>
                    <li class="flex items-center justify-between rounded-2xl bg-gray-50 px-4 py-3 dark:bg-white/5">
                        <span>Ticket #302 respondido</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Ontem</span>
                    </li>
                </ul>
            </div>

            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Próximas ações</h2>
                <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-300">
                    <div class="rounded-2xl border border-dashed border-indigo-300/60 p-4 dark:border-indigo-500/40">
                        <p class="font-medium text-indigo-700 dark:text-indigo-300">Configurar novos planos</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Defina limites de uso e recursos do plano avançado.</p>
                    </div>
                    <div class="rounded-2xl border border-dashed border-emerald-300/60 p-4 dark:border-emerald-500/40">
                        <p class="font-medium text-emerald-700 dark:text-emerald-300">Ativar rotinas de backup</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Garanta que o agendamento diário esteja configurado.</p>
                    </div>
                    <div class="rounded-2xl border border-dashed border-amber-300/60 p-4 dark:border-amber-500/40">
                        <p class="font-medium text-amber-700 dark:text-amber-300">Planejar automações de IA</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Revise prompts e integrações obrigatórias do V1.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
