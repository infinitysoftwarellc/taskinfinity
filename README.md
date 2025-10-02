# TaskInfinity

Aplicativo Laravel + Livewire para gerenciamento de tarefas com busca rápida via Scout / Meilisearch e painel de foco inspirado em TickTick.

## Desenvolvimento

- Instale dependências com `composer install` e `npm install`.
- Escolha o driver de busca com `SEARCH_DRIVER` (`local` usa SQL interno, `meilisearch` é opcional).
- Ao optar por Meilisearch, ajuste `MEILISEARCH_HOST` / `MEILISEARCH_KEY` no `.env`.
- Rode `php artisan migrate --seed` para preparar o banco local.
- Ative o bundler com `npm run dev` durante o desenvolvimento.

## Observabilidade (Laravel Pulse)

- Dashboard disponível em `/admin/pulse` (autenticado + verificação de e-mail).
- Monitora filas, jobs lentos, queries lentas e requisições Livewire (agrupadas como `livewire:*`).
- Ajuste thresholds e sample rate via variáveis `PULSE_*` em `.env` conforme necessário.

## Spotlight e Busca

- Atalho `Ctrl/Cmd + K` abre a paleta de comandos (tarefas, projetos e rotas comuns).
- Resultados usam Laravel Scout (`Task::search()`, `Project::search()`) com filtros por usuário atual.
- `SEARCH_DRIVER=local` executa as buscas via SQL; com `meilisearch` ativo o app captura falhas e volta para o modo local.
