<div class="tasks-workspace">
    <section class="tasks-panel tasks-list-panel">
        @if ($viewPayload)
            @php
                $totalViewTasks = $viewPayload['slug'] === 'all'
                    ? $viewPayload['lists']->sum(fn ($list) => $list['tasks']->count())
                    : $viewPayload['tasks']->count();
            @endphp

            <header class="tasks-list-header">
                <span class="tasks-list-header-meta">{{ $totalViewTasks }} tarefas</span>
                <h1 class="tasks-list-title">{{ $viewPayload['title'] }}</h1>
                <p class="tasks-list-description">{{ $viewPayload['description'] }}</p>
            </header>

            <div class="tasks-search-row">
                <div class="tasks-search-input">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                    </svg>
                    <input type="search" placeholder="Pesquisar tarefas" wire:model.debounce.400ms="search" />
                </div>
                <div class="tasks-search-hint">Use a busca para filtrar tarefas por título ou descrição.</div>
            </div>

            <div class="tasks-groups">
                @if ($viewPayload['slug'] === 'all')
                    @forelse ($viewPayload['lists'] as $viewList)
                        <div class="tasks-group">
                            <div class="tasks-group-header" style="cursor: default;">
                                <h2 class="tasks-list-title" style="font-size:18px; margin:0;">{{ $viewList['name'] }}</h2>
                                <span class="tasks-group-count">{{ $viewList['tasks']->count() }} tarefas</span>
                            </div>
                            <div class="tasks-group-body">
                                @foreach ($viewList['tasks'] as $task)
                                    <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-view-' . $viewList['id'] . '-' . $task->id" />
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="tasks-empty">
                            Nenhuma tarefa encontrada. Crie tarefas nas suas listas para vê-las aqui.
                        </div>
                    @endforelse
                @else
                    <div class="tasks-group-body">
                        @forelse ($viewPayload['tasks'] as $task)
                            <div>
                                @if ($task->relationLoaded('list') && $task->list)
                                    <span class="tasks-badge" style="margin-left: 6px; margin-bottom:6px; display:inline-flex;">{{ $task->list->name }}</span>
                                @endif
                                <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-view-' . $task->id" />
                            </div>
                        @empty
                            <div class="tasks-empty">
                                @if ($viewPayload['slug'] === 'today')
                                    Nenhuma tarefa com prazo para hoje.
                                @else
                                    Nenhuma tarefa com prazo para os próximos 7 dias.
                                @endif
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        @elseif ($list)
            <header class="tasks-list-header">
                <span class="tasks-list-header-meta">{{ $list->tasks_count }} tarefas</span>
                <h1 class="tasks-list-title">{{ $list->name }}</h1>
            </header>

            <div class="tasks-search-row">
                <div class="tasks-search-input">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15z" />
                    </svg>
                    <input type="search" placeholder="Pesquisar tarefas" wire:model.debounce.400ms="search" />
                </div>
                <div class="tasks-search-hint">
                    <span class="tasks-compact-text">Dica:</span> pressione <kbd>Shift</kbd> + <kbd>Enter</kbd> no nome da tarefa para criar uma subtarefa instantaneamente.
                </div>
            </div>

            <div class="tasks-add-form">
                <div class="tasks-input-field">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <input
                        id="new-task-title"
                        type="text"
                        placeholder="+ Adicionar tarefa"
                        wire:model.defer="newTaskTitle"
                        wire:keydown.enter.prevent="createRootTask"
                    />
                </div>
                <button type="button" class="tasks-input-action" wire:click="createRootTask">
                    Adicionar
                </button>
            </div>

            @error('newTaskTitle')
                <div class="tasks-error">{{ $message }}</div>
            @enderror

            <div class="tasks-groups">
                @forelse ($tasks as $task)
                    <livewire:task.item :task="$task" :depth="$task->depth ?? 0" :key="'task-item-' . $task->id" />
                @empty
                    <div class="tasks-empty">
                        Nenhuma tarefa cadastrada ainda. Crie a primeira tarefa no campo acima.
                    </div>
                @endforelse
            </div>
        @else
            <div class="tasks-detail-placeholder" style="padding: 40px; text-align:center;">
                <h2 class="tasks-list-title" style="font-size:20px; margin-bottom:12px;">Comece criando uma lista</h2>
                <p class="tasks-search-hint">Use o botão “+ New” na barra lateral para criar listas e organizar suas tarefas sem recarregar a página.</p>
            </div>
        @endif
    </section>

    <aside class="tasks-panel tasks-detail-panel">
        <div class="tasks-detail-header">
            <h4 class="tasks-detail-title">{{ $showEditor && $editorTask ? 'Editar tarefa' : 'Detalhes' }}</h4>
            @if ($showEditor && $editorTask)
                <button type="button" class="task-icon-btn" wire:click="closeEditor" style="width:34px; height:34px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M6 18 18 6" />
                    </svg>
                </button>
            @endif
        </div>

        <div class="tasks-detail-body">
            @if ($showEditor && $editorTask)
                <div>
                    <p class="tasks-compact-text">Editor avançado</p>
                    <h2 class="tasks-detail-task-title">{{ $editorTask->title }}</h2>
                </div>

                <hr class="tasks-detail-divider" />

                <form wire:submit.prevent="saveEditor" class="tasks-detail-form">
                    <div class="tasks-field">
                        <label>Nome</label>
                        <input type="text" wire:model.defer="editorForm.title" class="tasks-input" />
                        @error('editorForm.title')
                            <span class="tasks-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="tasks-detail-grid">
                        <div class="tasks-field">
                            <label>Status</label>
                            <select wire:model="editorForm.status" class="tasks-select">
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('editorForm.status')
                                <span class="tasks-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="tasks-field">
                            <label>Prioridade</label>
                            <select wire:model="editorForm.priority" class="tasks-select">
                                @foreach ($priorityOptions as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                            @error('editorForm.priority')
                                <span class="tasks-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="tasks-field">
                            <label>Prazo</label>
                            <input type="datetime-local" wire:model="editorForm.due_at" class="tasks-input" />
                            @error('editorForm.due_at')
                                <span class="tasks-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="tasks-field">
                            <label>Estimativa de pomodoros</label>
                            <input type="number" min="0" wire:model="editorForm.estimate_pomodoros" class="tasks-input" />
                            @error('editorForm.estimate_pomodoros')
                                <span class="tasks-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="tasks-field">
                            <label>Pomodoros concluídos</label>
                            <input type="number" min="0" wire:model="editorForm.pomodoros_done" class="tasks-input" />
                            @error('editorForm.pomodoros_done')
                                <span class="tasks-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="tasks-field" style="gap:12px;">
                        <div class="tasks-tags-manage" style="justify-content: space-between;">
                            <span>Tags</span>
                            <span class="tasks-compact-text">Opcional</span>
                        </div>

                        @if (empty($availableTags))
                            <div class="tasks-info-card">
                                Crie sua primeira tag abaixo para categorizar tarefas por contexto, prioridade ou cliente.
                            </div>
                        @else
                            <div class="tasks-tags-grid">
                                @foreach ($availableTags as $tag)
                                    <label for="editor-tag-{{ $tag['id'] }}" class="tasks-tag-pill">
                                        <span class="tasks-checkbox-label">
                                            <input id="editor-tag-{{ $tag['id'] }}" type="checkbox" value="{{ $tag['id'] }}" wire:model="editorTagIds" />
                                            <span class="tasks-tag-dot" style="background: {{ $tag['color'] }}"></span>
                                            <span style="text-transform:uppercase; letter-spacing:0.08em;">{{ $tag['name'] }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="tasks-detail-grid" style="grid-template-columns: minmax(0, 1fr) 120px;">
                            <div class="tasks-field">
                                <label>Nova tag</label>
                                <input type="text" wire:model.defer="newTagName" placeholder="ex: Cliente X" class="tasks-input" />
                                @error('newTagName')
                                    <span class="tasks-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="tasks-field">
                                <label>Cor</label>
                                <input type="color" wire:model="newTagColor" class="tasks-color-input" title="Escolha a cor da tag" />
                                <span class="tasks-compact-text">{{ strtoupper($newTagColor) }}</span>
                                @error('newTagColor')
                                    <span class="tasks-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="tasks-detail-actions" style="justify-content: flex-start;">
                            <button type="button" class="tasks-button-secondary" wire:click="createTag" wire:loading.attr="disabled" wire:target="createTag">
                                <span wire:loading.remove wire:target="createTag">Adicionar tag</span>
                                <span wire:loading wire:target="createTag">Salvando...</span>
                            </button>
                        </div>
                    </div>

                    <div class="tasks-field">
                        <label>Descrição</label>
                        <textarea rows="6" wire:model.defer="editorForm.description" class="tasks-textarea"></textarea>
                        @error('editorForm.description')
                            <span class="tasks-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="tasks-detail-actions">
                        <button type="button" class="tasks-button-secondary" wire:click="closeEditor">
                            Cancelar
                        </button>
                        <button type="submit" class="tasks-button-primary">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            @else
                <div class="tasks-detail-placeholder">
                    <p>Selecione uma tarefa para visualizar detalhes e editar status, prioridade, tags e descrição.</p>
                    <p>Use o duplo clique em uma tarefa para abrir o painel de detalhes aqui ao lado.</p>
                </div>
            @endif
        </div>
    </aside>
</div>
