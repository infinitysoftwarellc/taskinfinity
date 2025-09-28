// ---- IMPORTS PADRÃO DO SEU PROJETO (mantenha os que já existem) ----
// import './bootstrap'

// ---- LUCIDE (ESM via NPM) ----
import { createIcons, icons } from 'lucide';

/* ────────────────────────────────────────────────────────────────── */
/* UTIL: hidratar ícones (chamar sempre que o DOM sofrer mudanças)   */
/* ────────────────────────────────────────────────────────────────── */
function hydrateIcons() {
  try {
    // IMPORTANTE: no ESM, passe { icons }, senão não renderiza
    createIcons({ icons });
  } catch (e) {
    console.error('Falha ao renderizar Lucide:', e);
  }
}

/* ────────────────────────────────────────────────────────────────── */
/* UTIL: alternar seção com aria-expanded + display none             */
/* ────────────────────────────────────────────────────────────────── */
function toggleSection(btn, content) {
  const expanded = btn.getAttribute('aria-expanded') !== 'false';
  btn.setAttribute('aria-expanded', String(!expanded));
  if (content) content.style.display = expanded ? 'none' : '';
}

/* ────────────────────────────────────────────────────────────────── */
/* DELEGAÇÃO: clique em checkbox (.checkbox)                          */
/* ────────────────────────────────────────────────────────────────── */
function onCheckboxClick(target) {
  const cb = target.closest('.checkbox');
  if (!cb) return false;

  cb.classList.toggle('checked');
  cb.closest('.task, .subtask')?.classList.toggle('done');
  return true;
}

/* ────────────────────────────────────────────────────────────────── */
/* DELEGAÇÃO: expander de tarefa com subtarefas                       */
/* espera markup: 
   .task.has-subtasks [aria-expanded] + (irmão seguinte) .subtasks   */
/* ────────────────────────────────────────────────────────────────── */
function onTaskExpanderClick(target) {
  const exp = target.closest('.task.has-subtasks .expander');
  if (!exp) return false;

  const task = exp.closest('.task.has-subtasks');
  const next = task?.nextElementSibling; // .subtasks logo abaixo
  const open = task?.getAttribute('aria-expanded') !== 'false';
  task?.setAttribute('aria-expanded', String(!open));
  if (next?.classList.contains('subtasks')) {
    next.style.display = open ? 'none' : '';
  }
  return true;
}

/* ────────────────────────────────────────────────────────────────── */
/* DELEGAÇÃO: toggles com data-click                                  */
/*  - [data-click="toggle-sidebar"]                                   */
/*  - [data-click="toggle-workspace"]                                 */
/*  - [data-click="toggle-group"]  -> usa .group > .group-body        */
/*  - [data-click="toggle-subgroup"] -> usa .subgroup > .task-list    */
/* Ajuste os seletores conforme seu HTML.                             */
/* ────────────────────────────────────────────────────────────────── */
function onGenericToggles(target) {
  // toggle sidebar
  const btnSidebar = target.closest('[data-click="toggle-sidebar"]');
  if (btnSidebar) {
    document.querySelector('.sidebar')?.classList.toggle('open');
    return true;
  }

  // toggle workspace
  const btnWs = target.closest('[data-click="toggle-workspace"], [data-toggle="workspace"]');
  if (btnWs) {
    const wsContent =
      document.querySelector(btnWs.dataset.target) ||
      document.querySelector('.workspace-content');
    toggleSection(btnWs, wsContent);
    return true;
  }

  // toggle group
  const btnGroup = target.closest('[data-click="toggle-group"], [data-toggle="group"]');
  if (btnGroup) {
    const section = btnGroup.closest('.group');
    const body = section?.querySelector('.group-body');
    const expanded = section?.getAttribute('aria-expanded') !== 'false';
    section?.setAttribute('aria-expanded', String(!expanded));
    if (body) body.style.display = expanded ? 'none' : '';
    return true;
  }

  // toggle subgroup
  const btnSubgroup = target.closest('[data-click="toggle-subgroup"], [data-toggle="subgroup"]');
  if (btnSubgroup) {
    const sg = btnSubgroup.closest('.subgroup');
    const list = sg?.querySelector('.task-list');
    const expanded = sg?.getAttribute('aria-expanded') !== 'false';
    sg?.setAttribute('aria-expanded', String(!expanded));
    if (list) list.style.display = expanded ? 'none' : '';
    return true;
  }

  return false;
}

/* ────────────────────────────────────────────────────────────────── */
/* DELEGAÇÃO GLOBAL: 1 listener p/ toda a página                      */
/* ────────────────────────────────────────────────────────────────── */
function setupDelegatedClick() {
  if (document.__delegated_click_wired) return;
  document.__delegated_click_wired = true;

  document.addEventListener('click', (e) => {
    const t = e.target;

    // ordem: específicos → genéricos
    if (onCheckboxClick(t)) return;
    if (onTaskExpanderClick(t)) return;
    if (onGenericToggles(t)) return;
  });
}

/* ────────────────────────────────────────────────────────────────── */
/* INPUTS: criar subtarefas e tarefas via Enter                       */
/* ────────────────────────────────────────────────────────────────── */
function setupInputs(root = document) {
  // add subtask
  root.querySelectorAll('.add-subtask-input').forEach((input) => {
    if (input.dataset.wired === '1') return;
    input.dataset.wired = '1';

    input.addEventListener('keydown', (e) => {
      if (e.key !== 'Enter') return;
      const val = input.value.trim();
      if (!val) return;

      const container = input.closest('.subtasks');
      if (!container) return;

      const node = document.createElement('div');
      node.className = 'subtask';
      node.innerHTML =
        `<button class="checkbox" aria-label="marcar subtask"></button>` +
        `<div class="title"></div>` +
        `<div class="meta">Inbox</div>`;
      node.querySelector('.title').textContent = val;

      const anchor = input.closest('.add-subtask');
      container.insertBefore(node, anchor);
      input.value = '';
      // não precisa wireCheckbox: usamos delegação global
    });
  });

  // add main task
  const addInput = root.querySelector('.add-input');
  const taskList = root.querySelector('.task-list');
  if (
    addInput &&
    taskList &&
    addInput.dataset.wired !== '1' &&
    addInput.dataset.behavior !== 'livewire'
  ) {
    addInput.dataset.wired = '1';

    addInput.addEventListener('keydown', (e) => {
      if (e.key !== 'Enter') return;
      const val = addInput.value.trim();
      if (!val) return;

      const row = document.createElement('div');
      row.className = 'task';
      row.innerHTML = `
        <button class="checkbox" aria-label="marcar tarefa"></button>
        <div class="title-line"><span class="title"></span></div>
        <div class="meta">Inbox</div>
      `;
      row.querySelector('.title').textContent = val;
      taskList.prepend(row);
      addInput.value = '';
      // delegação cobre o click das novas checkboxes
    });
  }
}

/* ────────────────────────────────────────────────────────────────── */
/* BOOT: DOM pronto                                                   */
/* ────────────────────────────────────────────────────────────────── */
function boot(root = document) {
  hydrateIcons();       // 1) troca <i> → <svg>
  setupDelegatedClick(); // 2) um único listener que sobrevive a trocas
  setupInputs(root);     // 3) inputs que criam itens dinamicamente
}

/* DOMContentLoaded (primeiro carregamento) */
document.addEventListener('DOMContentLoaded', () => boot(document));

/* Livewire v3: re-hidratar ícones e re-wire inputs após navegação/morph */
document.addEventListener('livewire:init', () => {
  hydrateIcons();
  setupInputs(document);

  document.addEventListener('livewire:navigated', () => {
    hydrateIcons();
    setupInputs(document);
  });

  if (window.Livewire?.hook) {
    window.Livewire.hook('message.processed', () => {
      hydrateIcons();
      setupInputs(document);
    });
  }
});

/* Livewire v2 (se precisar): descomente
document.addEventListener('livewire:load', () => {
  window.Livewire.hook('message.processed', (message, component) => {
    hydrateIcons();
    setupInputs(component.el);
  });
});
*/

document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();
});
