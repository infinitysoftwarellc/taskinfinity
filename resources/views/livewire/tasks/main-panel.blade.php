<main class="main panel">
    <div class="toolbar">
        <div class="title">All <span class="bubble">38</span></div>
        <div class="spacer"></div>
        <button class="icon-btn" title="Ordenar"><i data-lucide="sort-desc"></i></button>
        <button class="icon-btn" title="Opções"><i data-lucide="more-horizontal"></i></button>
    </div>

    <div class="add-row">
        <input class="add-input" placeholder="Add task to 'Inbox'" data-add-task-input />
    </div>

    <section class="group" aria-expanded="true">
        <header class="group-header" data-action="toggle-group">
            <i class="chev" data-lucide="chevron-down"></i>
            <span class="group-title">No Date</span>
            <span class="group-count">38</span>
        </header>
        <div class="group-body">
            <div class="subgroup" aria-expanded="true">
                <div class="subgroup-toggle" data-action="toggle-subgroup">
                    <i class="chev" data-lucide="chevron-down"></i>
                    <span class="name">aa</span>
                    <span class="meta" style="margin-left:auto; color:var(--muted)">Inbox</span>
                </div>

                <div class="task-list">
                    <div class="task ghost">
                        <div class="checkbox" aria-hidden="true"></div>
                        <div class="title-line"><span class="title" style="opacity:.6">No Title</span></div>
                        <div class="meta">Inbox</div>
                    </div>

                    <div class="task has-subtasks" aria-expanded="true">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="expander" title="Expandir/ocultar subtarefas" data-action="toggle-subtasks"><i data-lucide="chevron-down"></i></div>
                        <div class="title-line"><span class="title">COLOCAR METAS</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>
                    <div class="subtasks">
                        <div class="subtask">
                            <button class="checkbox" data-action="toggle-checkbox"></button>
                            <div class="title">Definir metas trimestrais</div>
                            <div class="meta">Inbox</div>
                        </div>
                        <div class="subtask">
                            <button class="checkbox" data-action="toggle-checkbox"></button>
                            <div class="title">Mapear KPIs por lista</div>
                            <div class="meta">Inbox</div>
                        </div>
                        <div class="add-subtask">
                            <i data-lucide="plus"></i>
                            <input type="text" placeholder="Add subtask" class="add-subtask-input" data-add-subtask />
                        </div>
                    </div>

                    <div class="task">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="title-line"><span class="title">COLOCAR IA</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>

                    <div class="task">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="title-line"><span class="title">THEMA FLORESTAL</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>

                    <div class="task">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="title-line"><span class="title">THEMA GAMER</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>

                    <div class="task">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="title-line"><span class="title">ADICIONAR</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>

                    <div class="task">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="title-line"><span class="title">HABITOS</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>

                    <div class="task">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="title-line"><span class="title">POMODORO</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>

                    <div class="task">
                        <button class="checkbox" data-action="toggle-checkbox" aria-label="marcar"></button>
                        <div class="title-line"><span class="title">TUDO QUE FALTA</span></div>
                        <div class="meta">Task Infinity</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
