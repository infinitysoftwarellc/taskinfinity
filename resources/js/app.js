import Alpine from 'alpinejs';
import autoAnimate from '@formkit/auto-animate';
import axios from 'axios';
import { nanoid } from 'nanoid';
import Fuse from 'fuse.js';
import hotkeys from 'hotkeys-js';
import { createFocusTrap } from 'focus-trap';
import {
  autoUpdate,
  computePosition as floatingComputePosition,
  flip,
  offset,
  shift,
} from '@floating-ui/dom';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/pt-br';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import * as anime from 'animejs';
import Uppy from '@uppy/core';
import Dashboard from '@uppy/dashboard';
import JustValidate from 'just-validate';
import * as idbKeyval from 'idb-keyval';
import localforage from 'localforage';
import debounce from 'lodash.debounce';
import throttle from 'lodash.throttle';
import Clusterize from 'clusterize.js';
import ClipboardJS from 'clipboard';
import EditorJS from '@editorjs/editorjs';

import './pomodoro';

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(relativeTime);
dayjs.locale('pt-br');

window.dayjs = dayjs;
window.flatpickr = flatpickr;
window.autoAnimate = autoAnimate;
window.axios = axios;
window.tippy = tippy;

let sortableLoader;

async function loadSortable() {
  if (!sortableLoader) {
    sortableLoader = import('sortablejs').then((module) => {
      const Sortable = module?.default ?? module;
      if (!window.Sortable) {
        window.Sortable = Sortable;
      }

      return Sortable;
    });
  }

  return sortableLoader;
}

window.tiLibs = {
  axios,
  nanoid,
  Fuse,
  hotkeys,
  createFocusTrap,
  tippy,
  anime,
  Uppy,
  UppyDashboard: Dashboard,
  JustValidate,
  idbKeyval,
  localforage,
  debounce,
  throttle,
  Clusterize,
  ClipboardJS,
  EditorJS,
  loadSortable,
  loadVirtualList: async () => {
    const module = await import('virtual-list/vlist.js');
    return module?.default ?? module;
  },
};

document.addEventListener('alpine:init', () => {
  hotkeys.filter = () => true;
});

function autoAnimatePlugin(Alpine) {
  Alpine.directive('auto-animate', (el, { expression }, { effect, evaluateLater, cleanup }) => {
    let controller;
    let hasEvaluated = false;
    let lastValue;

    const start = (value) => {
      if (controller?.destroy) {
        controller.destroy();
      }

      controller = null;

      if (value === false) {
        return;
      }

      const config = typeof value === 'function' || (value && typeof value === 'object') ? value : undefined;
      controller = autoAnimate(el, config);
    };

    if (expression) {
      const evaluate = evaluateLater(expression);
      effect(() => {
        evaluate((value) => {
          if (hasEvaluated && value === lastValue) {
            return;
          }

          hasEvaluated = true;
          lastValue = value;
          start(value);
        });
      });
    } else {
      start();
    }

    cleanup(() => {
      if (controller?.destroy) {
        controller.destroy();
      }
      controller = null;
    });
  });
}

const defaultFloatingMiddleware = [offset(8), flip(), shift({ padding: 8 })];

function computeFloatingPosition(reference, floating, options = {}) {
  const { middleware = defaultFloatingMiddleware, placement = 'bottom-start', strategy = 'fixed', ...rest } = options;

  return floatingComputePosition(reference, floating, {
    middleware,
    placement,
    strategy,
    ...rest,
  });
}

window.tiFloating = {
  autoUpdate,
  computePosition: computeFloatingPosition,
  middleware: {
    offset,
    flip,
    shift,
  },
};

let inlineMenuOpenCount = 0;

function registerInlineMenuOpen() {
  inlineMenuOpenCount += 1;
  if (inlineMenuOpenCount === 1) {
    document.body.classList.add('ti-inline-menu-open');
  }
}

function registerInlineMenuClose() {
  if (inlineMenuOpenCount > 0) {
    inlineMenuOpenCount -= 1;
  }
  if (inlineMenuOpenCount === 0) {
    document.body.classList.remove('ti-inline-menu-open');
  }
}

function resolveMiddlewareChain(options = {}) {
  const chain = [];
  const { middleware = [], offset: offsetValue = 8 } = options;

  if (Array.isArray(middleware) && middleware.length) {
    return middleware;
  }

  if (typeof offset === 'function') {
    chain.push(offset(offsetValue));
  }

  if (typeof flip === 'function') {
    chain.push(flip());
  }

  if (typeof shift === 'function') {
    chain.push(shift({ padding: 8 }));
  }

  return chain;
}

function inlineMenuController(options = {}) {
  const { placement = 'bottom-end', offset: offsetValue = 8 } = options;

  return {
    open: false,
    placement,
    offset: offsetValue,
    middleware: options.middleware,
    triggerEl: null,
    dropdownEl: null,
    cleanup: null,
    init() {
      this.triggerEl = this.$refs.trigger;
      this.dropdownEl = this.$refs.dropdown;

      this.$watch('open', (value, oldValue) => {
        if (value === oldValue) {
          return;
        }

        if (value) {
          registerInlineMenuOpen();

          this.$nextTick(() => {
            this.updatePosition();

            if (autoUpdate && this.triggerEl && this.dropdownEl) {
              this.cleanup = autoUpdate(this.triggerEl, this.dropdownEl, () => this.updatePosition());
            }
          });
        } else {
          if (typeof this.cleanup === 'function') {
            this.cleanup();
            this.cleanup = null;
          }

          registerInlineMenuClose();
        }
      });
    },
    toggle() {
      if (this.open) {
        this.close(true);
      } else {
        this.show();
      }
    },
    show() {
      if (this.open) {
        return;
      }

      this.open = true;
    },
    close(focusTrigger = false) {
      if (!this.open) {
        return;
      }

      this.open = false;

      if (focusTrigger && this.triggerEl?.focus) {
        requestAnimationFrame(() => {
          this.triggerEl.focus({ preventScroll: false });
        });
      }
    },
    updatePosition() {
      if (!this.triggerEl || !this.dropdownEl || !window.tiFloating) {
        return;
      }

      window.tiFloating
        .computePosition(this.triggerEl, this.dropdownEl, {
          placement: this.placement,
          strategy: 'fixed',
          middleware: resolveMiddlewareChain({ middleware: this.middleware, offset: this.offset }),
        })
        .then(({ x, y, placement: resolvedPlacement }) => {
          Object.assign(this.dropdownEl.style, {
            inset: 'auto',
            position: 'fixed',
            left: `${x}px`,
            top: `${y}px`,
          });

          this.dropdownEl.dataset.placement = resolvedPlacement;
        })
        .catch(() => {});
    },
    destroy() {
      if (this.open) {
        this.open = false;
      }

      if (typeof this.cleanup === 'function') {
        this.cleanup();
        this.cleanup = null;
      }
    },
  };
}

window.tiInlineMenu = inlineMenuController;

function parseFlatpickrOptions(element) {
  const options = {};

  const raw = element.dataset.flatpickrOptions;
  if (raw) {
    try {
      Object.assign(options, JSON.parse(raw));
    } catch (error) {
      console.warn('Invalid data-flatpickr-options JSON', error);
    }
  }

  if (element.dataset.flatpickrEnableTime) {
    options.enableTime = element.dataset.flatpickrEnableTime !== 'false';
  }

  if (element.dataset.flatpickrDateFormat) {
    options.dateFormat = element.dataset.flatpickrDateFormat;
  }

  if (element.dataset.flatpickrAltFormat) {
    options.altInput = true;
    options.altFormat = element.dataset.flatpickrAltFormat;
  }

  if (element.dataset.flatpickrDefaultHour) {
    const hour = parseInt(element.dataset.flatpickrDefaultHour, 10);
    if (!Number.isNaN(hour)) {
      options.defaultHour = hour;
    }
  }

  if (element.dataset.flatpickrDefaultMinute) {
    const minute = parseInt(element.dataset.flatpickrDefaultMinute, 10);
    if (!Number.isNaN(minute)) {
      options.defaultMinute = minute;
    }
  }

  return options;
}

function initFlatpickrs(root = document) {
  const elements = [];

  if (root.matches?.('[data-flatpickr]:not([data-flatpickr-ready])')) {
    elements.push(root);
  }

  if (root.querySelectorAll) {
    elements.push(...root.querySelectorAll('[data-flatpickr]:not([data-flatpickr-ready])'));
  }

  elements.forEach((element) => {
    if (element._flatpickr) {
      element._flatpickr.destroy();
    }

    const options = parseFlatpickrOptions(element);

    const instance = flatpickr(element, options);
    element.dataset.flatpickrReady = '1';
    element._flatpickr = instance;
  });
}

window.tiInitFlatpickr = initFlatpickrs;
window.tiInitAutoAnimate = initAutoAnimateElements;

function formatRelativeTimestamp(element) {
  const iso = element.dataset.relativeDatetime;
  const fallback = element.dataset.relativeFallback ?? '';
  const timezoneName = element.dataset.relativeTz || null;
  const format = element.dataset.relativeFormat || 'fromNow';

  if (!iso) {
    if (fallback) {
      element.textContent = fallback;
    }
    return;
  }

  let instance;

  try {
    instance = timezoneName && dayjs.tz ? dayjs.tz(iso, timezoneName) : dayjs(iso);
  } catch (error) {
    instance = dayjs(iso);
  }

  if (!instance?.isValid()) {
    element.textContent = fallback || iso;
    return;
  }

  let label;

  switch (format) {
    case 'format': {
      const pattern = element.dataset.relativePattern || 'DD/MM/YYYY HH:mm';
      label = instance.format(pattern);
      break;
    }
    default:
      label = instance.fromNow();
  }

  const prefix = element.dataset.relativePrefix || '';
  const suffix = element.dataset.relativeSuffix || '';

  if (prefix) {
    label = `${prefix}${prefix.endsWith(' ') ? '' : ' '}${label}`;
  }

  if (suffix) {
    label = `${label}${suffix.startsWith(' ') ? '' : ' '}${suffix}`;
  }

  element.textContent = label;
}

function initRelativeDatetimes(root = document) {
  const elements = [];

  if (root.matches?.('[data-relative-datetime]')) {
    elements.push(root);
  }

  if (root.querySelectorAll) {
    elements.push(...root.querySelectorAll('[data-relative-datetime]'));
  }

  elements.forEach((element) => {
    formatRelativeTimestamp(element);
  });
}

function initAutoAnimateElements(root = document) {
  const elements = [];

  if (root.matches?.('[data-auto-animate]:not([data-auto-animate-ready])')) {
    elements.push(root);
  }

  if (root.querySelectorAll) {
    elements.push(...root.querySelectorAll('[data-auto-animate]:not([data-auto-animate-ready])'));
  }

  elements.forEach((element) => {
    autoAnimate(element);
    element.dataset.autoAnimateReady = '1';
  });
}

function normalizeSortableOptions(rawOptions) {
  if (!rawOptions) {
    return {};
  }

  try {
    const parsed = typeof rawOptions === 'string' ? JSON.parse(rawOptions) : rawOptions;
    return parsed && typeof parsed === 'object' ? parsed : {};
  } catch (error) {
    console.warn('Invalid wire:sortable options JSON', error);
    return {};
  }
}

function initWireSortable(root = document) {
  if (!window.Livewire) {
    return;
  }

  const containers = [];

  if (root.matches?.('[wire\\:sortable]:not([data-wire-sortable-ready])')) {
    containers.push(root);
  }

  if (root.querySelectorAll) {
    containers.push(...root.querySelectorAll('[wire\\:sortable]:not([data-wire-sortable-ready])'));
  }

  containers.forEach((container) => {
    const component = findLivewireComponent(container);
    if (!component) {
      return;
    }

    const expression = container.getAttribute('wire:sortable');
    const handle = container.dataset.sortableHandle || container.getAttribute('data-sortable-handle');
    const options = {
      animation: 150,
      draggable: '[wire\\:sortable\\.item]',
      handle: handle || undefined,
      onEnd: () => {
        const order = Array.from(container.querySelectorAll('[wire\\:sortable\\.item]'))
          .map((item) => item.getAttribute('wire:sortable.item'))
          .filter((value) => value !== null && value !== undefined && value !== '');

        if (!order.length) {
          return;
        }

        if (expression) {
          component.call(expression, order);
        } else {
          const eventName = container.dataset.sortableEvent || 'sortable-updated';
          component.dispatch(eventName, { order });
        }
      },
    };

    Object.assign(options, normalizeSortableOptions(container.dataset.sortableOptions));

    if (container.__wireSortableInstance) {
      container.__wireSortableInstance.destroy();
    }

    container.__wireSortableInstance = new Sortable(container, options);
    container.dataset.wireSortableReady = '1';
  });
}

window.tiInitWireSortable = initWireSortable;

Alpine.plugin(autoAnimatePlugin);

document.addEventListener('alpine:init', () => {
  Alpine.magic('dayjs', () => dayjs);
  Alpine.magic('floating', () => window.tiFloating);
  Alpine.magic('flatpickr', (el) => (options = {}) => {
    if (el._flatpickr) {
      el._flatpickr.destroy();
    }

    const instance = flatpickr(el, { ...parseFlatpickrOptions(el), ...options });
    el.dataset.flatpickrReady = '1';
    el._flatpickr = instance;
    return instance;
  });
});

window.Alpine = Alpine;
Alpine.start();

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
  const exp = target.closest('.task.has-subtasks .expander, .subtask.has-children .expander');
  if (!exp) return false;

  const hasWireClick = exp.getAttributeNames?.().some((name) => name.startsWith('wire:click'));
  if (hasWireClick) return false;

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
    if (container.dataset.sortablePending === '1') return;

    const component = findLivewireComponent(container);
    if (!component) {
      return;
    }

    const expression = container.getAttribute('wire:sortable');
    container.dataset.sortablePending = '1';

    loadSortable()
      .then((Sortable) => {
        if (!container.isConnected) {
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

            if (expression) {
              component.call(expression, ordered);
            } else {
              component.call('reorderMissions', ordered);
            }
          },
        });

        container.dataset.sortableReady = '1';
        container.dataset.wireSortableReady = '1';
        container.__sortable = sortable;
      })
      .catch((error) => {
        console.error('Failed to initialize mission sorting', error);
      })
      .finally(() => {
        delete container.dataset.sortablePending;
      });
  });
}

// Garante o arrastar-e-soltar das subtarefas dentro da página Tasks.
function setupSortableSubtasks(root = document) {
  const containers = root.querySelectorAll('[data-subtask-container]:not([data-sortable-ready])');

  containers.forEach((container) => {
    if (container.dataset.sortablePending === '1') return;

    const missionId = parseNullableInt(container.dataset.missionId);

    if (!missionId) {
      container.dataset.sortableReady = '1';
      return;
    }

    const component = findLivewireComponent(container);
    if (!component) {
      return;
    }

    const expression = container.getAttribute('wire:sortable');
    const isDetailsContainer =
      container.classList.contains('ti-subtask-list') ||
      container.classList.contains('ti-subtask-children');

    container.dataset.sortablePending = '1';

    loadSortable()
      .then((Sortable) => {
        if (!container.isConnected) {
          return;
        }

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

            const targetExpression = expression || 'reorderSubtasks';
            component.call(targetExpression, missionId, payload);
          },
        });

        container.dataset.sortableReady = '1';
        container.dataset.wireSortableReady = '1';
        container.__sortable = sortable;
      })
      .catch((error) => {
        console.error('Failed to initialize subtask sorting', error);
      })
      .finally(() => {
        delete container.dataset.sortablePending;
      });
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
  initFlatpickrs(root);
  initRelativeDatetimes(root);
  initAutoAnimateElements(root);
  initWireSortable(root);
  setupDelegatedClick(); // 2) um único listener que sobrevive a trocas
  setupInputs(root);     // 3) inputs que criam itens dinamicamente
  setupSortableTasks(root);
  setupSortableSubtasks(root);
  setupFocusListeners();
  setupDetailsKeyboard();
}

window.tiBoot = boot;

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
