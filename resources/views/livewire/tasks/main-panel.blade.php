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
        <button class="icon-btn" title="Ordenar"><i data-lucide="sort-desc"></i></button>
        <button class="icon-btn" title="Opções"><i data-lucide="more-horizontal"></i></button>
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
            <i data-lucide="plus"></i>
        </button>
    </form>
    @error('newTaskTitle')
        <p class="form-error">{{ $message }}</p>
    @enderror
    @error('newTaskListId')
        <p class="form-error">{{ $message }}</p>
    @enderror

    <section class="group" aria-expanded="true">
        <header class="group-header" data-toggle="group">
            <i class="chev" data-lucide="chevron-down"></i>
            <span class="group-title">{{ $primaryGroupTitle }}</span>
            <span class="group-count">{{ $totalCount }}</span>
        </header>
        <div class="group-body">
            @if ($unlistedMissions->isNotEmpty())
                <div class="subgroup" aria-expanded="true" wire:key="subgroup-unlisted">
                    <div class="subgroup-toggle" data-toggle="subgroup">
                        <i class="chev" data-lucide="chevron-down"></i>
                        <span class="name">Sem lista</span>
                        <span class="meta" style="margin-left:auto; color:var(--muted)">
                            {{ $unlistedMissions->count() }} tarefas
                        </span>
                    </div>

                    <div class="task-list">
                        @foreach ($unlistedMissions as $mission)
                            @php
                                $isActive = $mission->id === $selectedMissionId;
                            @endphp
                            <div
                                wire:key="mission-unlisted-{{ $mission->id }}"
                                wire:click="selectMission({{ $mission->id }})"
                                @class([
                                    'task',
                                    'done' => $mission->status === 'done',
                                    'is-active' => $isActive,
                                ])
                            >
                                <button class="checkbox" aria-label="Marcar tarefa" type="button"></button>
                                <div class="title-line"><span class="title">{{ $mission->title }}</span></div>
                                <div class="meta">Sem lista</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @foreach ($lists as $list)
                <div class="subgroup" aria-expanded="true" wire:key="subgroup-list-{{ $list->id }}">
                    <div class="subgroup-toggle" data-toggle="subgroup">
                        <i class="chev" data-lucide="chevron-down"></i>
                        <span class="name">{{ $list->name }}</span>
                        <span class="meta" style="margin-left:auto; color:var(--muted)">
                            {{ $list->missions->count() }} tarefas
                        </span>
                    </div>

                    <div class="task-list">
                        @forelse ($list->missions as $mission)
                            @php
                                $isActive = $mission->id === $selectedMissionId;
                            @endphp
                            <div
                                wire:key="mission-list-{{ $mission->id }}"
                                wire:click="selectMission({{ $mission->id }})"
                                @class([
                                    'task',
                                    'done' => $mission->status === 'done',
                                    'is-active' => $isActive,
                                ])
                            >
                                <button class="checkbox" aria-label="Marcar tarefa" type="button"></button>
                                <div class="title-line"><span class="title">{{ $mission->title }}</span></div>
                                <div class="meta">{{ $list->name }}</div>
                            </div>
                        @empty
                            <div class="task ghost">
                                <div class="checkbox" aria-hidden="true"></div>
                                <div class="title-line">
                                    <span class="title" style="opacity:.6">Sem tarefas nesta lista</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @if ($totalCount === 0)
        <div class="empty-state">
            <p>Nenhuma tarefa cadastrada ainda. Que tal criar a primeira?</p>
        </div>
    @endif
</main>
