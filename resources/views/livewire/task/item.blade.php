<div style="display: contents;">
    @if ($task->exists)
        <div class="task-item" style="margin-left: {{ max(0, $depth) * 1.5 }}rem;">
            <article
                class="task-card {{ $status === 'done' ? 'is-complete' : '' }}"
                wire:dblclick="openEditor"
            >
                <div class="task-card-main">
                    <button
                        type="button"
                        wire:click="toggleCompletion"
                        class="task-checkbox {{ $status === 'done' ? 'is-active' : '' }}"
                        aria-pressed="{{ $status === 'done' ? 'true' : 'false' }}"
                    >
                        <span>✓</span>
                    </button>

                    <input
                        type="text"
                        wire:model.debounce.400ms="title"
                        wire:keydown.enter.prevent="saveTitle"
                        wire:keydown.shift.enter.prevent="quickSubtask"
                        class="task-title-input"
                        placeholder="Nome da tarefa"
                    />
                </div>

                <div class="task-actions">
                    <button type="button" wire:click="toggleSubtaskForm" class="task-secondary-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Subtarefa
                    </button>
                    <button type="button" wire:click="deleteTask" class="task-icon-btn" title="Excluir tarefa">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </article>

            @error('title')
                <div class="tasks-error" style="padding-left: 14px;">{{ $message }}</div>
            @enderror

            @if ($showSubtaskForm)
                <div class="task-subtask-form">
                    <div class="task-subtask-form-row">
                        <input
                            type="text"
                            wire:model.defer="subtaskTitle"
                            wire:keydown.enter.prevent="createSubtask"
                            class="task-subtask-input"
                            placeholder="Digite o nome da subtarefa"
                        />
                        <button type="button" wire:click="createSubtask" class="tasks-button-primary" style="padding: 8px 18px; letter-spacing:0.08em;">
                            Adicionar
                        </button>
                    </div>
                    @error('subtaskTitle')
                        <div class="task-subtask-error">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            @if ($task->childrenRecursive->isNotEmpty())
                <div class="task-children">
                    @foreach ($task->childrenRecursive as $child)
                        <livewire:task.item :task="$child" :depth="$depth + 1" :key="'task-item-' . $child->id" />
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
