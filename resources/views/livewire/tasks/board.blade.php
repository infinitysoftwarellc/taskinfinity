<div class="tasks-board" data-task-board>

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
