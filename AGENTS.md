perfeito! vou ajustar o blueprint pro teu V1 vendável com as mudanças:

❌ sem multi-workspace agora

🤖 IA obrigatória (OpenAI-compatível)

✅ cada tarefa com até 7 subtarefas

📝 descrição simples com anexos

💾 backup óbvio (incluído)

💳 Stripe + (Mercado Pago/Pix) no V1

📤 exportar dados (CSV/JSON)

🛢️ MySQL

🛠️ SaaS simplificado: Admin com analytics, planos e suporte enxuto

Visão Geral (V1)

Stack: Laravel 11, PHP 8.2+, MySQL, Livewire v3 + Alpine + Tailwind, Pest, Spatie Laravel-Backup, Cashier (Stripe), SDK Mercado Pago (Pix/Cartão), Jobs/Queues (database), Scheduler (cron).

Módulos V1

Tarefas: Hoje/Amanhã/Próx. 7 dias/Caixa de entrada + Pastas/Listas/Etiquetas/Filtros; modos Lista / Kanban / Linha do tempo; subtarefas (máx 7); comentários; anexos.

Pomodoro (server-driven): start/pause/resume/stop, histórico, stats.

Hábitos: visão dia/semana/mês + streaks.

IA obrigatória: planejar tarefas por projeto/ideia, quebrar objetivos, autolabel, gerar checklist.

Gamificação leve: XP/moedas simples + “Loja” cosmética.

Assinaturas: planos, cobrança com Stripe e Mercado Pago.

Admin (SaaS): painel com analytics (uso/receita), gestão de planos, cupons, tickets de suporte simples, export geral.

Backup & Export: backup (DB+storage) e export CSV/JSON por usuário.

Tenancy (V1): sem workspaces/teams. App “single-tenant” por instância com multiusuário e assinatura por usuário. (Teams ficam pra V2.)

ERD (simplificado, sem workspace)

Usuários & Assinaturas

users (id, name, email, password, role enum[admin,user], …)

subscriptions (Cashier – Stripe)

subscription_items (Cashier)

mp_payments (id, user_id, mp_id, status, amount, currency, raw_json) – pagamentos Mercado Pago (Pix/cartão)

Organização de Tarefas

folders (id, user_id, name, position)

lists (id, user_id, folder_id?, name, position, view_mode enum[list,kanban,timeline])

tasks (id, user_id, list_id, title, description, due_date?, priority enum[none,low,med,high], status enum[todo,doing,done,archived], estimate_pomodoros int, pomodoros_done int, position)

subtasks (id, task_id, title, done bool, position) → app enforça máx. 7

task_tags (id, user_id, name, color)

task_tag_task (task_id, task_tag_id)

task_comments (id, task_id, user_id, body)

attachments (id, attachable_type, attachable_id, user_id, path, mime, size)

attachable permite anexar em descrição (task) e em comentários.

Filtros

filters (id, user_id, name, query_json)

Pomodoro

pomodoro_settings (id, user_id, focus, short_break, long_break, long_every)

pomodoro_sessions (id, user_id, task_id?, type enum[focus,short,long], started_at, duration_sec, ended_at?, status enum[running,finished,canceled,paused], remaining_sec?)

Hábitos

habits (id, user_id, name, schedule enum[daily,weekly,custom], custom_days json?, goal_per_period?, color)

habit_entries (id, habit_id, date, completed bool, value int)

IA

ai_prompts (id, user_id, title, body, output_json)

ai_provider_configs (id, user_id, api_base, api_key encrypted, model)

Gamificação

wallets (id, user_id, coins int, xp int, life int)

gami_events (id, user_id, type, value, meta json, created_at)

Suporte & Admin

tickets (id, user_id, subject, status enum[open,closed], priority enum[low,med,high])

ticket_messages (id, ticket_id, user_id nullable(admin), body)

plans (id, key, name, price_month, features_json, is_active)

coupons (id, code, percent_off?, amount_off?, valid_until?, max_redemptions?, redemptions)

Regras de Negócio (V1)

Subtarefas: máximo 7 por tarefa → validar em Service/Observer e no Controller (HTTP 422 se >7).

Descrição com anexos: editor simples (Markdown) + “Adicionar anexo” (upload para storage/app/public/attachments), linkando no corpo (ex.: [arquivo.pdf](/storage/attachments/…)).

Pomodoro: estado calculado no servidor (polling 5–10s). Finalização incrementa pomodoros_done, registra XP/moedas.

IA:

POST /ai/plan → gera tasks (titulo, subtarefas <=7, tags, prioridade sugerida)

POST /ai/autolabel → sugere etiquetas a partir do título/descrição

POST /ai/split → quebra objetivo em tarefas

Config via .env: AI_API_BASE, AI_API_KEY, AI_MODEL (OpenAI-compatível)

Assinaturas & Pay:

Stripe (Cashier) como caminho “feliz” (cartão).

Mercado Pago (Pix/cartão) como alternativa: cria mp_payments, webhooks atualizam status + concedem/estendem plano manualmente (simples).

Planos: Free (limites), Pro (sem limites razoáveis), Business (reservado p/ V2 com Teams).

Limites Free (sugestão): 3 listas, 200 tarefas ativas, IA 30 requisições/mês.

Rotas Principais
GET  /                      -> Dashboard (Hoje, pomodoro, hábitos do dia)
Auth Breeze/Fortify

# Tarefas
GET  /tasks?view=today|tomorrow|next7|inbox
GET  /folders
POST /folders
POST /lists
GET  /lists/{list}          -> list/kanban/timeline
POST /tasks
PUT  /tasks/{id}
DELETE /tasks/{id}
POST /tasks/{id}/comments
POST /tasks/{id}/attachments
POST /tasks/{id}/subtasks    -> cria (bloqueia se >=7)
PUT  /subtasks/{id}
DELETE /subtasks/{id}

# Tags / Filtros
POST /tags
POST /filters               -> salva consulta (query_json)

# Pomodoro
GET  /pomodoro
GET  /pomodoro/state
POST /pomodoro/start
POST /pomodoro/pause
POST /pomodoro/resume
POST /pomodoro/stop

# Hábitos
GET  /habits
POST /habits
POST /habits/{id}/tick

# IA (obrigatória)
POST /ai/plan
POST /ai/autolabel
POST /ai/split

# Gamificação
GET  /gamer
POST /shop/buy

# Billing (Stripe + MP)
GET  /billing
POST /billing/stripe/checkout
POST /billing/stripe/webhook
POST /billing/mp/checkout
POST /billing/mp/webhook

# Export
GET  /export/tasks.csv
GET  /export/tasks.json
GET  /export/full-zip       -> zip (JSON+CSV) + anexos

# Admin
GET  /admin                 -> painel
GET  /admin/analytics       -> gráficos
GET  /admin/users
GET  /admin/payments
GET  /admin/tickets
POST /admin/plans
POST /admin/coupons

Admin (SaaS) – o que entra no V1

Analytics:

Ativos 7/30 dias, novas contas, funil de ativação (criou 1ª lista/tarefa),

Tarefas criadas/completas por dia, pomodoros concluídos, hábitos marcados,

Receita (Stripe) por mês, LTV simples, MRR aproximado,

Conversões por cupom/plano.

Planos/Coupons: CRUD simples + toggle ativo/inativo.

Pagamentos: lista (Stripe events + MP), filtros por status.

Suporte: tickets (admin responde), fecha/abre.

Export geral (auditoria/export legal).

Validações Importantes (exemplos)

Subtarefas ≤ 7

// App/Actions/Tasks/CreateSubtask.php
$exists = Subtask::where('task_id', $taskId)->count();
abort_if($exists >= 7, 422, 'Máximo de 7 subtarefas por tarefa.');


IA – normalização de saída

// App/Services/AI/Planner.php
public function plan(string $prompt): array {
  $resp = $this->client->chat([
    'model' => config('ai.model'),
    'messages' => [['role'=>'user','content'=> $promptTemplate($prompt)]],
  ]);
  $json = $this->extractJson($resp);
  // garante até 7 subtarefas por item:
  foreach ($json['tasks'] as &$t) {
    $t['subtasks'] = array_slice($t['subtasks'] ?? [], 0, 7);
  }
  return $json;
}


Pomodoro – estado no servidor (mesma lógica do rascunho anterior).

Pagamentos (resumo de implementação)

Stripe (Cashier): plano mensal “pro_monthly” → user->newSubscription('default','pro_monthly')->checkout(). Webhook invoice.payment_succeeded confirma.

Mercado Pago: criar preferência (Pix ou cartão), redirecionar; webhook atualiza mp_payments.status e chama ActivatePlan::handle($user, $plan, period).

Fonte da verdade do plano: tabela users.plan_key, users.plan_ends_at. Cashier continua sendo usado para Stripe; para MP, setamos manualmente.

Backup & Export

Backup: spatie/laravel-backup → agenda diária; inclui DB e storage/app/public.

Export:

CSV/JSON de tarefas, subtarefas, hábitos, pomodoros (filtros por data).

Full ZIP: JSON master + CSVs + pasta de anexos (referências atualizadas no JSON).

Seeds e Limites (V1 demo)

Seed com: 1 usuário admin, 1 usuário comum (assinante Pro), 2 pastas, 4 listas, 20 tarefas (com 0–3 subtarefas), 5 tags, 3 hábitos, 10 sessões pomodoro.

Limites Free/Pro definidos em config/plans.php.

UI (rapidez e limpeza)

Livewire components:

Tasks/ListView, Tasks/Board (kanban), Tasks/Timeline

Tasks/Editor (markdown + anexar arquivo)

Pomodoro/Timer (polling), Pomodoro/Stats

Habits/Grid, Habits/MonthHeatmap

AI/Planner (gera preview → botão “aplicar”)

Billing/CheckoutStripe, Billing/CheckoutMP

Admin/Analytics, Admin/Tickets

Temas: claro/escuro + 4 paletas (CSS vars).

Gamer: painel com barra de XP e moedas; 5 conquistas iniciais.

Instalação (README V1)

composer install

cp .env.example .env (MySQL, storage)

php artisan key:generate

php artisan migrate --seed

php artisan storage:link

AI: AI_API_BASE, AI_API_KEY, AI_MODEL (obrigatório)

Stripe: STRIPE_KEY, STRIPE_SECRET

Mercado Pago: MP_ACCESS_TOKEN, MP_WEBHOOK_SECRET

Cron: * * * * * php artisan schedule:run

Queue: php artisan queue:work --daemon

Roadmap curto (V1 → V1.x)

Notificações de vencimento (email/in-app).

Import TickTick/CSV.

Kanban com colunas custom (status extra).

Relatórios PDF (tarefas/hábitos/pomodoro).

V2 (grande)

Multi-workspace/Teams, permissões avançadas, compartilhamento/colaboração em tempo real (WebSockets), integrações (Calendar), analytics por equipe, marketplace interno.
