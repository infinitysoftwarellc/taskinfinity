<main class="main panel">
    <div class="toolbar">
        <div class="title">
            {{ $primaryGroupTitle }}
            <span class="bubble">{{ $totalCount }}</span>
        </div>
        <div class="spacer"></div>
        @if ($listView)
            <a wire:navigate href="{{ route('tasks.index') }}" class="toolbar-link">Ver todas as tarefas</a>
        @endif
        <button class="icon-btn" title="Ordenar"><i class="fa-solid fa-arrow-down-wide-short" aria-hidden="true"></i></button>
        <button class="icon-btn" title="Opções"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></button>
    </div>

    <form class="add-row" wire:submit.prevent="createTask">
        <input
            wire:model.defer="newTaskTitle"
            class="add-input"
            type="text"
            placeholder="{{ $inputPlaceholder }}"
            data-behavior="livewire"
            aria-label="Adicionar nova tarefa"
        />

        @if ($showListSelector)
            <select wire:model="newTaskListId" class="add-select" aria-label="Selecionar lista">
                <option value="">Sem lista</option>
                @foreach ($availableLists as $listOption)
                    <option value="{{ $listOption->id }}">{{ $listOption->name }}</option>
                @endforeach
            </select>
        @endif

        <button class="icon-btn" type="submit" title="Adicionar tarefa" wire:loading.attr="disabled">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
        </button>
    </form>
    @error('newTaskTitle')
        <p class="form-error">{{ $message }}</p>
    @enderror
    @error('newTaskListId')
        <p class="form-error">{{ $message }}</p>
    @enderror

    <section class="group" aria-expanded="true">

        <div class="group-body">
            @php
                // Junta todas as tarefas (sem e com lista) numa coleção única
                $allMissions = collect($unlistedMissions ?? [])
                    ->concat(($lists ?? collect())->flatMap->missions)
                    // Opcional: ordena por created_at desc (remova se não quiser)
                    ->sortByDesc(fn($m) => $m->created_at ?? $m->id)
                    ->values();
                $maxSubtasks = $maxSubtasks ?? 7;
            @endphp

            <div class="task-list">
                @forelse ($allMissions as $mission)
                    @php
                        $isActive = $mission->id === $selectedMissionId;
                        $relationLoaded = method_exists($mission, 'relationLoaded') && $mission->relationLoaded('checkpointTree');
                        $subtasksRelation = $relationLoaded ? $mission->getRelation('checkpointTree') : collect();
                        $subtasks = collect($subtasksRelation);
                        $hasSubtasks = $subtasks->isNotEmpty();
                        $rootSubtaskCount = $subtasks->count();
                        $canAddMissionSubtask = $rootSubtaskCount < $maxSubtasks;
                    @endphp

                    <div class="task-block" wire:key="mission-flat-{{ $mission->id }}">
                        <div
                            wire:click="selectMission({{ $mission->id }})"
                            @class([
                                'task',
                                'done' => $mission->status === 'done',
                                'is-active' => $isActive,
                                'has-subtasks' => $hasSubtasks,
                            ])
                            @if($hasSubtasks)
                                aria-expanded="true"
                            @endif
                        >
                            <button class="checkbox" aria-label="Marcar tarefa" type="button"></button>
                            @if ($hasSubtasks)
                                <button class="expander" type="button" title="Recolher subtarefas" aria-label="Recolher subtarefas">
                                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                                </button>
                            @endif
                            <div class="title-line">
                                @if ($editingMissionId === $mission->id)
                                    <input
                                        type="text"
                                        class="inline-input"
                                        data-mission-input="{{ $mission->id }}"
                                        wire:model.defer="editingMissionTitle"
                                        wire:keydown.enter.prevent="saveMissionEdit({{ $mission->id }})"
                                        wire:keydown.shift.enter.prevent="missionShiftEnter({{ $mission->id }})"
                                        wire:keydown.escape="cancelMissionEdit"
                                        wire:blur="saveMissionEdit({{ $mission->id }})"
                                    />
                                @else
                                    <span class="title" wire:click.stop="startMissionEdit({{ $mission->id }})">
                                        {{ $mission->title ?: 'Sem título' }}
                                    </span>
                                @endif
                            </div>
                            <div class="task-actions" wire:click.stop>
                                @if ($canAddMissionSubtask)
                                    <button
                                        type="button"
                                        class="task-quick-btn"
                                        title="Adicionar subtarefa (Shift + Enter)"
                                        wire:click.stop="createSubtaskForMission({{ $mission->id }})"
                                    >
                                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                    </button>
                                @endif
                                @include('livewire.tasks.partials.inline-menu')
                            </div>
                            {{-- Não exibimos rótulo de lista para manter apenas tarefas puras --}}
                        </div>

                        <div class="subtasks">
                            @if ($hasSubtasks)
                                @foreach ($subtasks as $node)
                                    @include('livewire.tasks.partials.main-panel-subtask', [
                                        'item' => $node,
                                        'depth' => 0,
                                        'missionId' => $mission->id,
                                        'selectedSubtaskId' => $selectedSubtaskId,
                                        'editingSubtaskId' => $editingSubtaskId,
                                        'siblingsCount' => $rootSubtaskCount,
                                        'maxSubtasks' => $maxSubtasks,
                                    ])
                                @endforeach
                                @if (! $canAddMissionSubtask)
                                    <div class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</div>
                                @endif
                            @else
                                @if ($canAddMissionSubtask)
                                    <div class="subtasks-empty">
                                        <button
                                            type="button"
                                            class="subtasks-empty-btn"
                                            wire:click.stop="createSubtaskForMission({{ $mission->id }})"
                                        >
                                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                            <span>Adicionar subtarefa</span>
                                        </button>
                                        <span class="subtask-hint">Shift + Enter para criar subtarefa</span>
                                    </div>
                                @else
                                    <div class="subtasks-empty">
                                        <span class="subtasks-limit">Limite de {{ $maxSubtasks }} subtarefas atingido.</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="task ghost">
                        <div class="checkbox" aria-hidden="true"></div>
                        <div class="title-line">
                            <span class="title" style="opacity:.6">Sem tarefas</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @if ($totalCount === 0)
        <div class="empty-state">
            <p>Nenhuma tarefa cadastrada ainda. Que tal criar a primeira?</p>
        </div>
    @endif
</main>
