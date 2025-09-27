# Documento de Produto — To‑Do IA (Single‑User) para CodeCanyon

> **Resumo**: Aplicativo web de lista de tarefas inspirado no TickTick, porém focado em **uso individual** (sem equipes), com **IA assistiva**, **Pomodoro**, **temas personalizáveis** e um fluxo simples de assinatura **Pro** via Stripe (Cashier) **apenas para o próprio usuário**. Entrega uma experiência rápida (Laravel + Livewire), com páginas enxutas e usabilidade polida para vender na CodeCanyon.

---

## 1) Pitch / Proposta de Valor

* **Organize sua rotina em minutos** com listas, tarefas e subtarefas (até 7), filtros por Hoje/(removido) e marcadas.
* **Trabalhe melhor com IA**: gere subtarefas a partir de um título, resuma descrições longas e receba sugestões de rotina.
* **Foque de verdade** com Pomodoro integrado e estatísticas de foco.
* **Deixe com a sua cara**: temas claro/escuro + cores de acento, fontes e pequenos ajustes de densidade.
* **Sem complicação**: tudo pensado para **uma única pessoa** — zero fricção de times, convites ou permissões.

---

## 2) Público‑Alvo & Posicionamento

* Profissionais solo, freelancers e estudantes que precisam de foco e simplicidade.
* Quem quer um TickTick minimalista com IA e Pomodoro, sem colaboração.
* Compradores de CodeCanyon que buscam um projeto vendável, fácil de instalar e com diferencial claro (IA + foco individual).

---

## 3) Diferenciais

* **IA integrada e útil** (não apenas “gimmick”): gerar subtarefas, resumir, reescrever.
* **Foco em single‑user**: sem camadas de permissão, sem convites — fluxo muito mais rápido.
* **UX pragmática**: atalhos de teclado, drag‑and‑drop, busca rápida, filtros de um clique.
* **Personalização visual** para aumentar adesão: temas e acento de cor.
* **Exportação/backup** simples para quem gosta de controle dos dados.

---

## 4) Escopo da V1 (Funcionalidades)

### 4.1 Tarefas & Listas

* **Listas** pessoais (sem compartilhamento).
* **Tarefas** com: título, descrição (markdown leve), prioridade (3 níveis), **rótulos**, estrela (favorita), data e hora opcionais, repetição simples (diária, semanal, mensal), **posição ordenável**.
* **Subtarefas**: 0 a 7 por tarefa.
* **Anexos básicos**: imagens e PDFs (limite por plano).
* **Ações rápidas**: concluir, marcar com estrela, mover de lista, arrastar posição.

### 4.2 Vistas/Filtros

* **Inbox (Caixa de entrada)**, **Marcadas**, **Concluídas**, **Rótulos**, **Arquivadas**.
* **Busca rápida** por texto/label.

### 4.3 Pomodoro & Foco

* Timer padrão 25/5 (configurável em preferências).
* Registro automático de sessões e **estatísticas** simples (tempo focado por dia/semana*, contagem por lista/tarefa).

  * *Sem prazos/datas de tarefa: estatísticas são temporais apenas para Pomodoro, não para vencimentos.

### 4.4 IA Assistiva (toggle por plano)

* **Gerar subtarefas** a partir do título/descrição.
* **Resumir** descrições/comentários longos.
* **Reescrever/clarear** o texto da tarefa.

### 4.5 Personalização & Temas

* Tema **Claro/Escuro** + cor de acento.
* Preferências: densidade (compacta/normal), fonte (2–3 opções), tamanho do texto.

### 4.6 Dados & Portabilidade

* **Exportar** tarefas (CSV/JSON) por lista ou tudo.
* **Backup/restore** simples (arquivo único JSON).

### 4.7 Contas & Assinatura (Single‑User)

* Conta individual com cadastro/login e e‑mail verificado.
* Página **Plano Pro** (Stripe via Cashier) para desbloquear IA, anexos maiores e temas extras.

### 4.8 Temas dinâmicos (sem telas de loading)

* **Troca instantânea** de tema e assets (cores, background, ícones, sons) sem telas de carregamento; transições suaves.
* **Padrão**: experiência neutra e minimalista (baseline).
* **Gamer**: visual neon/alto contraste; terminologia e gamificação nativas.
* **Florestal**: visual orgânico, foco em objetivos de longo prazo e rituais.

#### 4.8.1 Tema Gamer — features exclusivas

* **Terminologia**: “tarefas” → **missões**; subtarefas → **checkpoints**.
* **Gamificação**: **XP**, **vida**, **personagem** (avatar evolutivo), **habilidades** (perks simples como +5% foco ao completar 3 pomodoros seguidos).
* **Mercado de troca**: loja interna de **skins/sons/ícones** adquiridos com XP (sem dinheiro real).
* **Notificações personalizadas**: toques/sons e textos temáticos ("Boss fight em 10min: seu foco começa agora!").
* **Pomodoro personalizado**: barras/medidores temáticos, conquistas leves ("Combo de Foco x3").
* **IA temática**: sugerir **missões** e **checkpoints** a partir de objetivos; sugestões de build (foco/energia) com base no histórico de uso.

#### 4.8.2 Tema Florestal — features exclusivas

* **Metas & Objetivos**: páginas para **Grandes Metas** (macro‑objetivos) e **Rituais** (rotinas semanais), inexistentes no Padrão/Gamer.
* **Cores & Backgrounds exclusivos**: paleta natural; **um fundo exclusivo** para o site inteiro.
* **Som ambiente**: trilhas leves (pássaros/vento) opcionais; alertas do Pomodoro temáticos.
* **Pomodoro consciente**: modos “ciclo leve” e “imersão” com micro‑pausas guiadas.
* **IA temática**: transformar objetivos longos em **planos de etapas** ("do bosque ao topo").

> Observação: Os temas **compartilham o núcleo** (listas, tarefas, subtarefas, filtros, export, Pro etc.), mas **renomeiam elementos** e **exibem páginas/elementos extras** quando aplicável.

## 5) Fora de Escopo (V1)

* **Sem equipes/colaboração/convites**.

* Comentários por tarefa (pode entrar na V1.1, se desejado).

* Calendário completo (mês/semana), integrações externas (Google Calendar), automações avançadas.

* Mobile app nativo (foco na web responsiva).

* **Sem equipes/colaboração/convites**.

* Comentários por tarefa (pode entrar na V1.1, se desejado).

* Calendário completo (mês/semana), integrações externas (Google Calendar), automações avançadas.

* Mobile app nativo (foco na web responsiva).

---

## 6) Páginas & Navegação

1. **Landing (opcional para demo)**: herói, benefícios, prints, comparação Free vs Pro, FAQ.
2. **Cadastro/Entrar/Recuperar senha**.
3. **Onboarding**: breve tour (3–5 steps) + **escolha de tema inicial** (Padrão, Gamer, Florestal — alterável depois) + criação de 3 listas de exemplo.
4. **Painel** (Home de trabalho): visão geral sem datas (tarefas recentes, marcadas, progresso de Pomodoro), quick add.
5. **Listas** (sidebar): navegação e gestão de listas/labels.
6. **Workspace**: filtros, drag‑and‑drop, busca.
7. **Detalhe da Tarefa** (ou **Missão** no Gamer): descrição, subtarefas (**checkpoints** no Gamer), anexos, IA.
8. **Pomodoro**: timer + histórico + **visual/sons** de acordo com o tema.
9. **Rótulos**: gerenciar e aplicar rótulos.
10. **Busca**: resultado unificado por texto/label.
11. **Configurações**: Perfil, **Tema** (troca **instantânea**, sem loading), Preferências (densidade, fonte, Pomodoro), Dados (export/backup), **Plano Pro**.
12. **Ajuda/FAQ**.

### Páginas exclusivas por tema

* **Gamer**

  * **Personagem**: avatar, nível, habilidades ativas.
  * **Mercado**: loja de itens cosméticos comprados com XP (sons, ícones, backgrounds alternativos do tema Gamer).
  * **Conquistas**: lista de achievements simples (somente cosméticos/xp).
* **Florestal**

  * **Grandes Metas**: cadastro de objetivos macro (ex: "Projeto Raiz"), com etapas geradas pela IA.
  * **Rituais**: rotinas semanais/mensais com indicadores suaves ("regar hábitos").
  * **Refúgio**: painel de foco com som ambiente e fundo exclusivo.

---

## 7) Fluxos Principais

* **Criar Tarefa Rápida**: campo fixo + Enter → opção de adicionar data/label depois.
* **Gerar Subtarefas com IA**: no detalhe da tarefa → botão IA → pré‑visualização → inserir.
* **Pomodoro**: iniciar/pausar/concluir → log automático → ver estatísticas.
* **Organização**: arrastar tarefa entre listas e reordenar.
* **Upgrade Pro**: abrir página de plano → checkout Stripe → acesso imediato a IA/temas/limites maiores.
* **Exportar/Backup**: Configurações → exportar CSV/JSON ou baixar backup completo.

---

## 8) Planos (exemplo sugerido)

* **Free** (individual):

  * Listas ilimitadas* e até **300 tarefas** totais.
  * **Subtarefas até 7**.
  * **Anexos** até 5 MB por arquivo.
  * Temas Claro/Escuro básicos.
  * Pomodoro e estatísticas básicas.
* **Pro** (individual):

  * Tudo do Free + **IA** (p. ex. 300 requisições/mês).
  * Anexos até **50 MB** por arquivo.
  * **Temas extras** (paletas e fontes adicionais).
  * Estatísticas avançadas (foco por lista/semana, melhores horários).

> *“Ilimitadas” pode ser “ilimitadas razoáveis” conforme documentação da listagem.

---

## 9) Conteúdo para a Listagem na CodeCanyon

### Título sugerido

**To‑Do IA com Pomodoro — App de Tarefas Single‑User (Laravel + Livewire)**

### Subtítulo/Resumo curto

Organize tarefas, foque com Pomodoro e use IA para gerar subtarefas e resumos. Single‑user, rápido, bonito e simples de instalar.

### Destaques (bullets)

* ✅ Single‑user: sem fricção de equipes
* 🤖 IA que ajuda de verdade (gerar subtarefas, resumir, reescrever)
* ⏱️ Pomodoro integrado + estatísticas
* 🎨 Temas claro/escuro e acento de cor
* 🔎 Vistas úteis (Hoje, Próximos 7, Marcadas) e busca
* 📦 Exportar dados (CSV/JSON) + backup/restore
* 💳 Plano Pro via Stripe (Cashier) — simples e direto

### FAQ (exemplos)

* **Precisa de servidor dedicado?** Não — hospedagem PHP 8.3+ com MySQL e Redis opcionais para filas.
* **Tem colaboração/equipe?** Não. Este produto é focado em **uso individual**.
* **Posso personalizar o tema?** Sim, há temas e acentos; o código facilita novas variações.
* **A IA é obrigatória?** Não. Está disponível no Pro e pode ser desativada.
* **Dá para exportar meus dados?** Sim, CSV/JSON e backup único.

### Requisitos (para a página)

* PHP 8.3+, MySQL 8+, Node 20 (build), extensões padrão do Laravel.

### Itens visuais (screenshots/GIFs sugeridos)

1. Painel + Quick Add
2. Workspace (drag‑and‑drop)
3. Detalhe da tarefa + IA (pré‑visualização)
4. Pomodoro + estatísticas
5. Temas (claro/escuro)
6. Export/Backup
7. Página Pro/Checkout

### Changelog inicial

* **v1.0.0** — Lançamento inicial (tarefas, subtarefas, filtros, IA, Pomodoro, temas, export/backup, plano Pro single‑user).

---

## 10) Roadmap (90 dias)

* **1.1**: comentários simples por tarefa; mais atalhos de teclado; import CSV de outros apps.
* **1.2**: calendário mensal; widgets de hábitos básicos; estatísticas por horário.
* **1.3**: automações leves (ex: mover para Hoje quando vencer), web‑push.

---

## 11) Critérios de Aceitação (amostra)

* **Transições sem loading**: troca de tema, navegação entre páginas principais e abertura do painel de detalhes **sem telas de carregamento**; micro‑transições fluidas.
* **Tema Gamer**:

  * Terminologia aplicada globalmente (“missões”, “checkpoints”).
  * XP sobe ao concluir missões e ciclos de Pomodoro; feedback visual imediato.
  * Mercado acessível e itens cosméticos equipáveis (sons/ícones/backgrounds do tema Gamer).
  * Pomodoro com skin Gamer e notificações temáticas.
* **Tema Florestal**:

  * Páginas **Grandes Metas**, **Rituais** e **Refúgio** disponíveis apenas quando o tema está ativo.
  * Cor e **background exclusivos** aplicados em toda a UI; som ambiente opcional.
  * IA gera planos de etapas para metas longas com linguagem temática.
* **Criar tarefa**: ao salvar, aparece no topo da lista target; teclado: Enter salva, Shift+Enter nova linha.
* **Subtarefas**/**Checkpoints**: máximo **7**; podem ser concluídas independentemente.
* **IA**: gerar entre 3–7 subtarefas/checkpoints com pré‑visualização.
* **Pomodoro**: registra início/fim; estatísticas exibem total diário; persistência após refresh; aplica skin conforme tema.
* **Exportar**: arquivo CSV/JSON válido contendo campos principais.
* **Temas**: toggle claro/escuro e troca de tema principal persistem entre sessões.
* **Plano Pro**: após checkout, IA e limites ampliados são liberados imediatamente.

---

## 12) Restrições & Políticas

* O app não oferece multiusuário colaborativo.
* IA: exibir aviso de privacidade e opção de opt‑out.
* Limites devem estar claros em **Configurações → Plano** e na documentação do produto.

---

## 13) Mapa Rápido (Páginas × Funcionalidades)

* **Hoje**: visão do dia, quick add, iniciar Pomodoro.
* **Listas**: CRUD de listas, navegação lateral.
* **Workspace**: filtros, ordenação, busca, drag‑and‑drop.
* **Tarefa (detalhe)**: descrição, subtarefas (≤7), IA, anexos, prioridade, data/hora.
* **Pomodoro**: timer, histórico, estatísticas.
* **Rótulos**: criar/renomear/apagar.
* **Busca**: resultados por texto/label.
* **Configurações**: perfil, preferências, tema, Pomodoro, dados (export/backup), plano Pro.
* **Ajuda/FAQ**: dúvidas comuns, política de dados.

---

> **Próximo passo**: posso transformar este documento em um **brief de design** (wireframes por página + cópia de UI), ou gerar o **texto pronto da landing/CodeCanyon** com variações de título e bullets.
