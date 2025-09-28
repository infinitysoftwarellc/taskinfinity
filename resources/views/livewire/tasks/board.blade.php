<div class="tasks-board" data-task-board>
    @once
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        </style>
    @endonce

    @once
        <style>
            .tasks-board {
                --bg: #0b0d12;
                --panel: #12151c;
                --panel-2: #0f1218;
                --hover: #181c25;
                --border: #1f2430;
                --text: #e7ebf3;
                --muted: #97a2b2;
                --muted-2: #6d7585;
                --accent: #7aa2ff;
                --brand: #7aa2ff;
                --success: #2ecc71;
                --warning: #ffcc66;
                --radius: 16px;
                --shadow: 0 10px 30px -12px rgba(0, 0, 0, .55);
                font: 14px/1.4 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji';
                color: var(--text);
                background: var(--bg);
            }

            .tasks-board,
            .tasks-board * {
                box-sizing: border-box;
            }

            .tasks-board .app {
                height: 100vh;
                padding: 12px;
                gap: 12px;
                display: grid;
                grid-template-columns: 64px 280px 1fr 420px;
                grid-template-rows: 1fr;
                grid-template-areas: 'rail sidebar main details';
            }

            .tasks-board .panel {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
            }

            .tasks-board .rail {
                grid-area: rail;
                background: var(--panel-2);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 8px 6px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .tasks-board .rail .avatar {
                width: 36px;
                height: 36px;
                border-radius: 12px;
                background: linear-gradient(135deg, #7aa2ff, #a78bfa);
                box-shadow: 0 0 0 2px #10141c;
            }

            .tasks-board .rail .btn {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: grid;
                place-items: center;
                border: 1px solid var(--border);
                color: var(--muted);
                background: transparent;
                cursor: pointer;
            }

            .tasks-board .rail .btn:hover {
                background: var(--hover);
                color: var(--text);
            }

            .tasks-board .rail .spacer {
                flex: 1;
            }

            .tasks-board .sidebar {
                grid-area: sidebar;
                padding: 12px;
                overflow: auto;
            }

            .tasks-board .sidebar h6 {
                margin: 14px 8px 6px;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .14em;
                color: var(--muted);
                font-weight: 700;
            }

            .tasks-board .nav-list {
                list-style: none;
                margin: 6px 0 10px;
                padding: 0;
            }

            .tasks-board .nav-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 10px;
                border-radius: 12px;
                color: var(--text);
                text-decoration: none;
            }

            .tasks-board .nav-item:hover {
                background: var(--hover);
            }

            .tasks-board .nav-item .icon {
                width: 18px;
                height: 18px;
                color: var(--muted);
            }

            .tasks-board .nav-item .label {
                flex: 1;
            }

            .tasks-board .nav-item .count {
                font-size: 12px;
                font-weight: 700;
                color: #cfe0ff;
                background: rgba(122, 162, 255, .12);
                border: 1px solid rgba(122, 162, 255, .3);
                padding: .15rem .45rem;
                border-radius: 999px;
            }

            .tasks-board .workspace {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 10px;
                cursor: pointer;
                border-radius: 10px;
                width: 100%;
                border: none;
                background: transparent;
                color: inherit;
                font: inherit;
                text-align: left;
            }

            .tasks-board .workspace:hover {
                background: var(--hover);
            }

            .tasks-board .workspace .chev {
                width: 16px;
                height: 16px;
                color: var(--muted);
                transition: transform .2s ease;
            }

            .tasks-board .workspace[aria-expanded="false"] .chev {
                transform: rotate(-90deg);
            }

            .tasks-board .workspace .title {
                font-weight: 600;
                font-size: 13px;
                color: var(--text);
            }

            .tasks-board .workspace .badge {
                margin-left: auto;
                font-size: 12px;
                color: #cfe0ff;
                background: rgba(122, 162, 255, .12);
                border: 1px solid rgba(122, 162, 255, .3);
                padding: .15rem .45rem;
                border-radius: 999px;
            }

            .tasks-board .filters-tip {
                background: var(--panel-2);
                border: 1px dashed var(--border);
                color: var(--muted);
                padding: 10px;
                border-radius: 12px;
                font-size: 12px;
                line-height: 1.3;
            }

            .tasks-board .tags {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .tasks-board .tag {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 10px;
                border-radius: 10px;
                color: var(--text);
                text-decoration: none;
            }

            .tasks-board .tag:hover {
                background: var(--hover);
            }

            .tasks-board .dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
            }

            .tasks-board .completed {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px;
                border-radius: 10px;
                color: var(--muted);
            }

            .tasks-board .completed:hover {
                background: var(--hover);
                color: var(--text);
            }

            .tasks-board .main {
                grid-area: main;
                overflow: auto;
                display: flex;
                flex-direction: column;
            }

            .tasks-board .toolbar {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 14px 16px;
                border-bottom: 1px solid var(--border);
            }

            .tasks-board .title {
                font-size: 20px;
                font-weight: 700;
                letter-spacing: .01em;
            }

            .tasks-board .title .bubble {
                margin-left: 8px;
                font-size: 12px;
                font-weight: 700;
                color: #cfe0ff;
                background: rgba(122, 162, 255, .12);
                border: 1px solid rgba(122, 162, 255, .3);
                padding: .15rem .5rem;
                border-radius: 999px;
            }

            .tasks-board .toolbar .spacer {
                flex: 1;
            }

            .tasks-board .icon-btn {
                display: inline-grid;
                place-items: center;
                width: 28px;
                height: 28px;
                border-radius: 8px;
                border: 1px solid var(--border);
                background: transparent;
                color: var(--muted);
                cursor: pointer;
            }

            .tasks-board .icon-btn:hover {
                background: var(--hover);
                color: var(--text);
            }

            .tasks-board .add-row {
                padding: 10px 16px;
                border-bottom: 1px solid var(--border);
            }

            .tasks-board .add-input {
                width: 100%;
                border: 1px solid var(--border);
                background: var(--panel-2);
                color: var(--text);
                border-radius: 12px;
                padding: 10px 12px;
                outline: none;
            }

            .tasks-board .add-input::placeholder {
                color: var(--muted);
            }

            .tasks-board .group {
                padding: 12px 0;
            }

            .tasks-board .group-header {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 6px 16px;
                user-select: none;
                cursor: pointer;
                color: var(--muted);
                font-weight: 700;
                text-transform: none;
                border: none;
                background: transparent;
            }

            .tasks-board .group-header .chev {
                width: 18px;
                height: 18px;
                color: var(--muted-2);
                transition: transform .2s ease;
            }

            .tasks-board .group[aria-expanded="false"] .chev {
                transform: rotate(-90deg);
            }

            .tasks-board .group-title {
                font-size: 12px;
                letter-spacing: .04em;
                text-transform: none;
                color: var(--muted);
            }

            .tasks-board .group-count {
                margin-left: 6px;
                color: #cfe0ff;
                font-size: 12px;
                background: rgba(122, 162, 255, .12);
                border: 1px solid rgba(122, 162, 255, .3);
                padding: .15rem .45rem;
                border-radius: 999px;
            }

            .tasks-board .subgroup {
                margin: 4px 0 8px;
            }

            .tasks-board .subgroup-toggle {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 16px;
                cursor: pointer;
                color: var(--text);
                border: none;
                background: transparent;
                width: 100%;
                text-align: left;
            }

            .tasks-board .subgroup .chev {
                width: 16px;
                height: 16px;
                color: var(--muted);
                transition: transform .2s ease;
            }

            .tasks-board .subgroup[aria-expanded="false"] .chev {
                transform: rotate(-90deg);
            }

            .tasks-board .subgroup .name {
                font-weight: 600;
            }

            .tasks-board .task-list {
                display: flex;
                flex-direction: column;
            }

            .tasks-board .task {
                display: grid;
                grid-template-columns: 28px 1fr auto;
                align-items: center;
                gap: 10px;
                padding: 8px 16px;
            }

            .tasks-board .task:hover {
                background: var(--hover);
            }

            .tasks-board .checkbox {
                width: 16px;
                height: 16px;
                border-radius: 4px;
                background: transparent;
                border: 1px solid var(--border);
                position: relative;
                cursor: pointer;
                display: inline-block;
            }

            .tasks-board .checkbox.checked {
                background: linear-gradient(135deg, var(--brand), #a78bfa);
                border-color: transparent;
            }

            .tasks-board .checkbox.checked::after {
                content: "";
                position: absolute;
                inset: 2px 4px 4px 2px;
                border-right: 2px solid white;
                border-bottom: 2px solid white;
                transform: rotate(40deg);
            }

            .tasks-board .task .title-line {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .tasks-board .task .title-line .title {
                font-size: 14px;
                font-weight: 600;
            }

            .tasks-board .task .meta {
                color: var(--muted);
                font-size: 12px;
            }

            .tasks-board .task.done .title {
                color: var(--muted);
                text-decoration: line-through;
                font-weight: 500;
            }

            .tasks-board .task.has-subtasks {
                grid-template-columns: 28px 16px 1fr auto;
            }

            .tasks-board .expander {
                width: 16px;
                height: 16px;
                display: grid;
                place-items: center;
                color: var(--muted);
                cursor: pointer;
            }

            .tasks-board .expander svg {
                width: 16px;
                height: 16px;
                transition: transform .2s ease;
            }

            .tasks-board .task[aria-expanded="false"] .expander svg {
                transform: rotate(-90deg);
            }

            .tasks-board .subtasks {
                padding: 0 16px 4px 60px;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .tasks-board .subtasks .subtask {
                display: grid;
                grid-template-columns: 16px 1fr auto;
                align-items: center;
                gap: 8px;
                padding: 6px 0;
            }

            .tasks-board .subtasks .checkbox {
                width: 14px;
                height: 14px;
                border-radius: 3px;
            }

            .tasks-board .subtasks .title {
                font-size: 13px;
                font-weight: 500;
            }

            .tasks-board .subtasks .meta {
                font-size: 11px;
                color: var(--muted);
            }

            .tasks-board .subtask.done .title {
                text-decoration: line-through;
                color: var(--muted);
            }

            .tasks-board .add-subtask {
                margin: 6px 0 8px;
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .tasks-board .add-subtask input {
                flex: 1;
                border: 1px solid var(--border);
                background: var(--panel-2);
                color: var(--text);
                border-radius: 10px;
                padding: 8px 10px;
                outline: none;
                font-size: 13px;
            }

            .tasks-board .details {
                grid-area: details;
                overflow: auto;
                display: flex;
                flex-direction: column;
            }

            .tasks-board .details .header {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 14px 16px;
                border-bottom: 1px solid var(--border);
            }

            .tasks-board .details .header .right {
                margin-left: auto;
                display: flex;
                gap: 8px;
            }

            .tasks-board .empty {
                padding: 20px 16px;
                color: var(--muted);
            }

            .tasks-board .sep {
                height: 1px;
                background: var(--border);
                margin: 10px 0;
            }

            .tasks-board *::-webkit-scrollbar {
                width: 10px;
                height: 10px;
            }

            .tasks-board *::-webkit-scrollbar-thumb {
                background: #1b2230;
                border-radius: 10px;
                border: 2px solid #0e1219;
            }

            .tasks-board *::-webkit-scrollbar-track {
                background: transparent;
            }

            @media (max-width: 1200px) {
                .tasks-board .app {
                    grid-template-columns: 64px 240px 1fr 360px;
                }
            }

            @media (max-width: 980px) {
                .tasks-board .app {
                    grid-template-columns: 64px 220px 1fr;
                    grid-template-areas: 'rail sidebar main' 'rail sidebar details';
                    grid-template-rows: 55% 45%;
                }
            }

            @media (max-width: 720px) {
                .tasks-board .app {
                    grid-template-columns: 1fr;
                    grid-template-areas: 'main';
                }

                .tasks-board .sidebar,
                .tasks-board .details,
                .tasks-board .rail {
                    display: none;
                }
            }
        </style>
    @endonce

    @once
        <script defer src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
        <script>
            function initTaskBoardInteractions() {
                const root = document.querySelector('[data-task-board]');

                if (!root || root.dataset.initialized === 'true') {
                    if (root && window.lucide) {
                        window.lucide.createIcons();
                    }

                    return;
                }

                root.dataset.initialized = 'true';

                const refreshIcons = () => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                };

                refreshIcons();

                root.addEventListener('click', (event) => {
                    const checkbox = event.target.closest('[data-action="toggle-checkbox"]');

                    if (checkbox && root.contains(checkbox)) {
                        event.preventDefault();
                        event.stopPropagation();
                        checkbox.classList.toggle('checked');
                        const row = checkbox.closest('.task, .subtask');

                        if (row) {
                            row.classList.toggle('done');
                        }

                        return;
                    }

                    const workspaceToggle = event.target.closest('[data-action="toggle-workspace"]');

                    if (workspaceToggle && root.contains(workspaceToggle)) {
                        const content = root.querySelector(workspaceToggle.dataset.target);
                        const expanded = workspaceToggle.getAttribute('aria-expanded') !== 'false';
                        workspaceToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');

                        if (content) {
                            content.style.display = expanded ? 'none' : '';
                        }

                        return;
                    }

                    const groupHeader = event.target.closest('[data-action="toggle-group"]');

                    if (groupHeader && root.contains(groupHeader)) {
                        const group = groupHeader.closest('.group');

                        if (group) {
                            const body = group.querySelector('.group-body');
                            const expanded = group.getAttribute('aria-expanded') !== 'false';
                            group.setAttribute('aria-expanded', expanded ? 'false' : 'true');

                            if (body) {
                                body.style.display = expanded ? 'none' : '';
                            }
                        }

                        return;
                    }

                    const subgroupToggle = event.target.closest('[data-action="toggle-subgroup"]');

                    if (subgroupToggle && root.contains(subgroupToggle)) {
                        const subgroup = subgroupToggle.closest('.subgroup');

                        if (subgroup) {
                            const list = subgroup.querySelector('.task-list');
                            const expanded = subgroup.getAttribute('aria-expanded') !== 'false';
                            subgroup.setAttribute('aria-expanded', expanded ? 'false' : 'true');

                            if (list) {
                                list.style.display = expanded ? 'none' : '';
                            }
                        }

                        return;
                    }

                    const expander = event.target.closest('[data-action="toggle-subtasks"]');

                    if (expander && root.contains(expander)) {
                        const task = expander.closest('.task.has-subtasks');

                        if (task) {
                            const subtasks = task.nextElementSibling;
                            const expanded = task.getAttribute('aria-expanded') !== 'false';
                            task.setAttribute('aria-expanded', expanded ? 'false' : 'true');

                            if (subtasks && subtasks.classList.contains('subtasks')) {
                                subtasks.style.display = expanded ? 'none' : '';
                            }
                        }

                        return;
                    }
                });

                root.addEventListener('keydown', (event) => {
                    if (event.target.matches('[data-add-subtask]') && event.key === 'Enter') {
                        const input = event.target;
                        const value = input.value.trim();

                        if (!value) {
                            return;
                        }

                        event.preventDefault();

                        const container = input.closest('.subtasks');

                        if (!container) {
                            return;
                        }

                        const node = document.createElement('div');
                        node.className = 'subtask';
                        node.innerHTML = `
                            <button class="checkbox" data-action="toggle-checkbox"></button>
                            <div class="title"></div>
                            <div class="meta">Inbox</div>
                        `;
                        node.querySelector('.title').textContent = value;
                        container.insertBefore(node, input.closest('.add-subtask'));
                        input.value = '';

                        return;
                    }

                    if (event.target.matches('[data-add-task-input]') && event.key === 'Enter') {
                        const input = event.target;
                        const value = input.value.trim();

                        if (!value) {
                            return;
                        }

                        event.preventDefault();

                        const list = root.querySelector('.task-list');

                        if (!list) {
                            return;
                        }

                        const row = document.createElement('div');
                        row.className = 'task';
                        row.innerHTML = `
                            <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                            <div class="title-line"><span class="title"></span></div>
                            <div class="meta">Inbox</div>
                        `;
                        row.querySelector('.title').textContent = value;
                        list.prepend(row);
                        input.value = '';
                    }
                });

                const observer = new MutationObserver((mutations) => {
                    for (const mutation of mutations) {
                        if (mutation.addedNodes.length > 0) {
                            refreshIcons();
                            break;
                        }
                    }
                });

                observer.observe(root, { childList: true, subtree: true });
            }

            document.addEventListener('DOMContentLoaded', initTaskBoardInteractions);
            document.addEventListener('livewire:load', initTaskBoardInteractions);
            document.addEventListener('livewire:navigated', initTaskBoardInteractions);
        </script>
    @endonce

    <div class="app">
        <livewire:tasks.rail />
        <livewire:tasks.sidebar />
        <livewire:tasks.main-panel />
        <livewire:tasks.details />
    </div>
</div>
