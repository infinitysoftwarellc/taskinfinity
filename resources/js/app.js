import Sortable from 'sortablejs';

import './pomodoro';

// ---- IMPORTS PADRÃO DO SEU PROJETO (mantenha os que precisar)

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
function onCheckboxClick(event) {
  const target = event.target;
  const cb = target.closest('.checkbox');
  if (!cb) return false;

  event.preventDefault();
  event.stopPropagation();

  cb.classList.toggle('checked');
  cb.closest('.task, .subtask')?.classList.toggle('done');
  return true;
}

/* ────────────────────────────────────────────────────────────────── */
/* DELEGAÇÃO: expander de tarefa com subtarefas                       */
/* espera markup: 
   .task.has-subtasks [aria-expanded] + (irmão seguinte) .subtasks   */
/* ────────────────────────────────────────────────────────────────── */
function onTaskExpanderClick(event) {
  const target = event.target;
  const exp = target.closest('.task.has-subtasks .expander');
  if (!exp) return false;

  event.preventDefault();
  event.stopPropagation();

  const task = exp.closest('.task.has-subtasks');
  if (task) {
    const next = task.nextElementSibling; // .subtasks logo abaixo
    const open = task.getAttribute('aria-expanded') !== 'false';
    const isExpanded = !open;
    task.setAttribute('aria-expanded', String(isExpanded));
    if (next?.classList.contains('subtasks')) {
      next.style.display = isExpanded ? '' : 'none';
    }

    const icon = exp.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-chevron-down', isExpanded);
      icon.classList.toggle('fa-chevron-right', !isExpanded);
    }

    return true;
  }

  const subtask = exp.closest('.subtask.has-children');
  if (!subtask) return false;

  const branch = subtask.nextElementSibling;
  const open = subtask.getAttribute('aria-expanded') !== 'false';
  const isExpanded = !open;
  subtask.setAttribute('aria-expanded', String(isExpanded));
  if (branch?.classList.contains('subtask-group')) {
    branch.style.display = isExpanded ? '' : 'none';
  }

  const icon = exp.querySelector('i');
  if (icon) {
    icon.classList.toggle('fa-chevron-down', isExpanded);
    icon.classList.toggle('fa-chevron-right', !isExpanded);
  }

  return true;
}

/* ────────────────────────────────────────────────────────────────── */
/* MENUS FLOTANTES                                                    */
/* ────────────────────────────────────────────────────────────────── */
function setMenuState(menu, open) {
  if (!menu) return;
  menu.classList.toggle('is-open', open);
  const trigger = menu.querySelector('[data-menu-trigger]');
  if (trigger) {
    trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  const inlineDropdown = menu.querySelector('.ti-inline-dropdown');
  if (inlineDropdown) {
    inlineDropdown.setAttribute('aria-hidden', open ? 'false' : 'true');
  }

  const simpleDropdown = menu.querySelector('.ti-menu-dropdown');
  if (simpleDropdown) {
    simpleDropdown.setAttribute('aria-hidden', open ? 'false' : 'true');
  }
}

function closeAllMenus(except) {
  document.querySelectorAll('[data-menu].is-open').forEach((menu) => {
    if (!except || menu !== except) {
      setMenuState(menu, false);
    }
  });

  if (!document.querySelector('[data-menu].is-open')) {
    document.body.classList.remove('ti-inline-menu-open');
  }
}

function onMenuToggle(event) {
  const target = event.target;
  const trigger = target.closest('[data-menu-trigger]');
  if (!trigger) return false;

  event.preventDefault();
  event.stopPropagation();

  const container = trigger.closest('[data-menu]');
  if (!container) return false;

  const isOpen = container.classList.contains('is-open');
  const willOpen = !isOpen;

  closeAllMenus(container);
  setMenuState(container, willOpen);

  if (willOpen) {
    document.body.classList.add('ti-inline-menu-open');
  } else if (!document.querySelector('[data-menu].is-open')) {
    document.body.classList.remove('ti-inline-menu-open');
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
function onGenericToggles(event) {
  const target = event.target;
  // toggle sidebar
  const btnSidebar = target.closest('[data-click="toggle-sidebar"]');
  if (btnSidebar) {
    event.preventDefault();
    event.stopPropagation();
    document.querySelector('.sidebar')?.classList.toggle('open');
    return true;
  }

  // toggle workspace — só para <button data-toggle="workspace">
  const btnWs = target.closest('button[data-click="toggle-workspace"], button[data-toggle="workspace"]');
  if (btnWs) {
    event.preventDefault();
    event.stopPropagation();
    if (target.closest('.workspace-add')) return true; // não colapsar ao clicar no “+”

    const container = btnWs.closest('.workspace');
    let wsContent = container?.nextElementSibling;
    if (!wsContent || !wsContent.classList?.contains('workspace-content')) {
      wsContent =
        document.querySelector(btnWs.dataset.target) ||
        document.querySelector('.workspace-content');
    }

    toggleSection(btnWs, wsContent);
    const state = btnWs.getAttribute('aria-expanded') ?? 'false';
    if (container) container.setAttribute('aria-expanded', state);
    return true;
  }

  // toggle group
  const btnGroup = target.closest('[data-click="toggle-group"], [data-toggle="group"]');
  if (btnGroup) {
    event.preventDefault();
    event.stopPropagation();
    const section = btnGroup.closest('.group');
    const body = section?.querySelector('.group-body');
    const expanded = section?.getAttribute('aria-expanded') !== 'false';
    const isExpanded = !expanded;
    section?.setAttribute('aria-expanded', String(isExpanded));
    if (body) body.style.display = isExpanded ? '' : 'none';
    const icon = btnGroup.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-chevron-down', isExpanded);
      icon.classList.toggle('fa-chevron-right', !isExpanded);
    }
    return true;
  }

  // toggle subgroup
  const btnSubgroup = target.closest('[data-click="toggle-subgroup"], [data-toggle="subgroup"]');
  if (btnSubgroup) {
    event.preventDefault();
    event.stopPropagation();
    const sg = btnSubgroup.closest('.subgroup');
    const list = sg?.querySelector('.task-list');
    const expanded = sg?.getAttribute('aria-expanded') !== 'false';
    const isExpanded = !expanded;
    sg?.setAttribute('aria-expanded', String(isExpanded));
    if (list) list.style.display = isExpanded ? '' : 'none';
    const icon = btnSubgroup.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-chevron-down', isExpanded);
      icon.classList.toggle('fa-chevron-right', !isExpanded);
    }
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

  if (!document.__menu_keyboard_wired) {
    document.__menu_keyboard_wired = true;
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && document.querySelector('[data-menu].is-open')) {
        closeAllMenus();
      }
    });
  }

  document.addEventListener(
    'click',
    (e) => {
      const t = e.target;

      const dismiss = t.closest('[data-menu-dismiss]');
      if (dismiss) {
        e.preventDefault();
        e.stopPropagation();
        closeAllMenus();
        return true;
      }

      if (!t.closest('[data-menu]')) {
        closeAllMenus();
      }

      // ordem: específicos → genéricos
      if (onCheckboxClick(e)) return;
      if (onTaskExpanderClick(e)) return;
      if (onMenuToggle(e)) return;

      const menuItem = t.closest('[data-menu-item]');
      if (menuItem) {
        closeAllMenus();
        return;
      }

      if (onGenericToggles(e)) return;
    },
    { capture: true }
  );
}

function findLivewireComponent(element) {
  if (!element) return null;

  const root = element.closest('[wire\\:id]');
  if (!root) return null;

  return window.Livewire?.find(root.getAttribute('wire:id')) ?? null;
}

function parseNullableInt(value) {
  if (value === undefined || value === null || value === '') {
    return null;
  }

  const parsed = parseInt(value, 10);
  return Number.isNaN(parsed) ? null : parsed;
}

function collectSubtaskOrder(container) {
  return Array.from(container.children || [])
    .filter((child) => child.matches?.('[data-subtask-node]'))
    .map((child) => {
      const id = parseInt(child.dataset.subtaskId || '', 10);
      return Number.isNaN(id) ? null : { id };
    })
    .filter(Boolean);
}

// Inicializa o arrastar-e-soltar das missões na página Tasks.
function setupSortableTasks(root = document) {
  const containers = root.querySelectorAll('[data-sortable-tasks]:not([data-sortable-ready])');

  containers.forEach((container) => {
    const component = findLivewireComponent(container);
    if (!component) {
      return;
    }

    const sortable = new Sortable(container, {
      animation: 150,
      draggable: '[data-mission-id]',
      handle: '.task',
      group: { name: 'missions' },
      onEnd: () => {
        const ordered = Array.from(container.querySelectorAll('[data-mission-id]'))
          .map((item) => {
            const id = parseInt(item.dataset.missionId || '', 10);
            if (Number.isNaN(id)) return null;

            return {
              id,
              list_id: parseNullableInt(item.dataset.listId),
            };
          })
          .filter(Boolean);

        if (!ordered.length) {
          return;
        }

        component.call('reorderMissions', ordered);
      },
    });

    container.dataset.sortableReady = '1';
    container.__sortable = sortable;
  });
}

// Garante o arrastar-e-soltar das subtarefas dentro da página Tasks.
function setupSortableSubtasks(root = document) {
  const containers = root.querySelectorAll('[data-subtask-container]:not([data-sortable-ready])');

  containers.forEach((container) => {
    const missionId = parseNullableInt(container.dataset.missionId);

    if (!missionId) {
      container.dataset.sortableReady = '1';
      return;
    }

    const component = findLivewireComponent(container);
    if (!component) {
      return;
    }

    const isDetailsContainer =
      container.classList.contains('ti-subtask-list') ||
      container.classList.contains('ti-subtask-children');

    const sortable = new Sortable(container, {
      animation: 160,
      swapThreshold: 0.18,
      fallbackOnBody: true,
      dragClass: 'is-dragging',
      ghostClass: 'is-ghost',
      group: { name: `subtasks-${missionId}`, pull: true, put: true },
      draggable: '[data-subtask-node]',
      handle: isDetailsContainer ? '.ti-subtask-row' : '.subtask',
      onEnd: (evt) => {
        const movedEl = evt.item;
        const movedId = parseInt(movedEl?.dataset?.subtaskId || '', 10);

        if (!movedId) {
          return;
        }

        const toContainer = evt.to.closest('[data-subtask-container]');
        const fromContainer = evt.from.closest('[data-subtask-container]');

        if (!toContainer || !fromContainer) {
          return;
        }

        const toParentId = parseNullableInt(toContainer.dataset.parentId);
        const fromParentId = parseNullableInt(fromContainer.dataset.parentId);

        if (fromContainer === toContainer && evt.oldIndex === evt.newIndex && toParentId === fromParentId) {
          return;
        }

        const toOrder = collectSubtaskOrder(toContainer);
        const fromOrder = fromContainer === toContainer ? toOrder : collectSubtaskOrder(fromContainer);

        const payload = {
          moved_id: movedId,
          to_parent_id: toParentId,
          from_parent_id: fromParentId,
          to_order: toOrder,
        };

        if (fromContainer !== toContainer) {
          payload.from_order = fromOrder;
        }

        component.call('reorderSubtasks', missionId, payload);
      },
    });

    container.dataset.sortableReady = '1';
    container.__sortable = sortable;
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

function focusInlineTarget(selector, attempts = 5) {
  requestAnimationFrame(() => {
    const el = document.querySelector(selector);
    if (!el) {
      if (attempts <= 0) return;
      setTimeout(() => focusInlineTarget(selector, attempts - 1), 40);
      return;
    }

    if (typeof el.focus === 'function') {
      el.focus({ preventScroll: false });
    }

    if (typeof el.setSelectionRange === 'function') {
      const len = el.value?.length ?? 0;
      el.setSelectionRange(len, len);
    } else if (el.isContentEditable) {
      const range = document.createRange();
      range.selectNodeContents(el);
      range.collapse(false);
      const selection = window.getSelection();
      if (!selection) return;
      selection.removeAllRanges();
      selection.addRange(range);
    }
  });
}

function setupFocusListeners() {
  if (document.__inline_focus_wired) return;
  document.__inline_focus_wired = true;

  window.addEventListener('focus-mission-input', (event) => {
    const missionId = event.detail?.missionId;
    if (!missionId) return;
    focusInlineTarget(`[data-mission-input="${missionId}"]`);
  });

  window.addEventListener('focus-subtask-input', (event) => {
    const subtaskId = event.detail?.subtaskId;
    if (!subtaskId) return;
    focusInlineTarget(`[data-subtask-input="${subtaskId}"]`);
  });
}

function setupDetailsKeyboard() {
  if (document.__details_keyboard_wired) return;
  document.__details_keyboard_wired = true;

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' || !event.shiftKey) return;

    const target = event.target;
    if (!target) return;
    if (['INPUT', 'TEXTAREA'].includes(target.tagName)) return;
    if (target.isContentEditable) return;

    const details = target.closest('.ti-details');
    if (!details) return;

    const componentRoot = details.closest('[wire\\:id]');
    if (!componentRoot) return;

    const component = window.Livewire?.find(componentRoot.getAttribute('wire:id'));
    if (!component) return;

    const selectedId = details.getAttribute('data-selected-subtask');

    event.preventDefault();

    if (selectedId) {
      component.call('openSubtaskForm', parseInt(selectedId, 10));
    } else {
      component.call('openSubtaskForm');
    }
  });
}

/* ────────────────────────────────────────────────────────────────── */
/* BOOT: DOM pronto                                                   */
/* ────────────────────────────────────────────────────────────────── */
function boot(root = document) {
  setupDelegatedClick(); // 2) um único listener que sobrevive a trocas
  setupInputs(root);     // 3) inputs que criam itens dinamicamente
  setupSortableTasks(root);
  setupSortableSubtasks(root);
  setupFocusListeners();
  setupDetailsKeyboard();
}

/* DOMContentLoaded (primeiro carregamento) */
document.addEventListener('DOMContentLoaded', () => boot(document));

/* Livewire v3: re-hidratar ícones e re-wire inputs após navegação/morph */
document.addEventListener('livewire:init', () => {
  boot(document);

  document.addEventListener('livewire:navigated', () => {
    boot(document);
  });

  if (window.Livewire?.hook) {
    window.Livewire.hook('message.processed', (_message, component) => {
      boot(component?.el ?? document);
    });
  }
});

/* Livewire v2 (se precisar): descomente
document.addEventListener('livewire:load', () => {
  window.Livewire.hook('message.processed', (message, component) => {
    setupInputs(component.el);
  });
});
*/

document.addEventListener('DOMContentLoaded', () => {
  // Toggle dos grupos de subtarefas
  document.querySelectorAll('[data-toggle="group"]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const expanded = btn.getAttribute('aria-expanded') !== 'false';
      btn.setAttribute('aria-expanded', String(!expanded));
      const list = document.getElementById(btn.getAttribute('aria-controls'));
      if (!list) return;
      const isExpanded = !expanded;
      if (list) {
        list.style.display = isExpanded ? '' : 'none';
      }
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-chevron-down', isExpanded);
        icon.classList.toggle('fa-chevron-right', !isExpanded);
      }
    });
  });
});
