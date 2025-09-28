@php
    $workspaceStyle = $workspaceExpanded ? 'padding-left:8px;' : 'padding-left:8px; display:none;';
@endphp

<aside class="sidebar panel">
    <h6>Atalhos</h6>
    <nav>
        <ul class="nav-list">
            @forelse ($shortcuts as $index => $shortcut)
                <li wire:key="shortcut-{{ $index }}">
                    <a class="nav-item {{ ($shortcut['active'] ?? false) ? 'is-active' : '' }}" href="{{ $shortcut['href'] ?? '#' }}">
                        <i class="icon" data-lucide="{{ $shortcut['icon'] ?? '' }}"></i>
                        <span class="label">{{ $shortcut['label'] ?? '' }}</span>
                        @if (($shortcut['count'] ?? 0) > 0)
                            <span class="count">{{ $shortcut['count'] }}</span>
                        @endif
                    </a>
                </li>
            @empty
                <li class="muted">Nenhum atalho configurado.</li>
            @endforelse
        </ul>
    </nav>

    <div class="sep"></div>

    <div class="workspace" aria-expanded="{{ $workspaceExpanded ? 'true' : 'false' }}" data-toggle="workspace">
        <button class="workspace-toggle" type="button">
            <i class="chev" data-lucide="chevron-down"></i>
            <span class="title">{{ $workspaceTitle ?? 'Workspace' }}</span>
            @if ($workspaceBadge)
                <span class="badge">{{ $workspaceBadge }}</span>
            @endif
        </button>
        <button class="btn-new" type="button" title="Nova lista" wire:click.prevent="toggleListForm">
            <i data-lucide="plus"></i>
        </button>
    </div>

    @if ($showListForm)
        <form class="sidebar-form" wire:submit.prevent="createList">
            <div class="form-row">
                <input
                    wire:model.defer="newListName"
                    class="sidebar-input"
                    type="text"
                    placeholder="Nome da lista"
                />
            </div>
            <div class="form-row">
                <input
                    wire:model.defer="newListIcon"
                    class="sidebar-input"
                    type="text"
                    placeholder="Ícone (lucide) opcional"
                />
            </div>
            <div class="form-row">
                <label class="sidebar-color">
                    <span>Cor</span>
                    <input wire:model.defer="newListColor" type="color" />
                </label>
            </div>
            @error('newListName')
                <p class="form-error">{{ $message }}</p>
            @enderror
            <div class="form-actions">
                <button class="btn-primary" type="submit">Salvar</button>
                <button class="btn-link" type="button" wire:click="toggleListForm">Cancelar</button>
            </div>
        </form>
    @endif

    <div class="workspace-content" style="{{ $workspaceStyle }}">
        <ul class="nav-list">
            @forelse ($lists as $list)
                <li wire:key="workspace-item-{{ $list->id }}">
                    <a class="nav-item" href="#">
                        <i class="icon" data-lucide="{{ $list->icon ?? 'list-todo' }}"></i>
                        <span class="label">{{ $list->name }}</span>
                        <span class="count">{{ $list->missions_count }}</span>
                    </a>
                </li>
            @empty
                <li class="muted">Nenhuma lista criada ainda.</li>
            @endforelse
        </ul>
    </div>

    <h6>Filters</h6>
    <div class="filters-tip">{{ $filtersTip }}</div>

    <div class="sidebar-section-header">
        <h6>Tags</h6>
        <button class="btn-new" type="button" title="Nova tag" wire:click.prevent="toggleTagForm">
            <i data-lucide="plus"></i>
        </button>
    </div>

    @if ($showTagForm)
        <form class="sidebar-form" wire:submit.prevent="createTag">
            <div class="form-row">
                <input
                    wire:model.defer="newTagName"
                    class="sidebar-input"
                    type="text"
                    placeholder="Nome da tag"
                />
            </div>
            <div class="form-row">
                <label class="sidebar-color">
                    <span>Cor</span>
                    <input wire:model.defer="newTagColor" type="color" />
                </label>
            </div>
            @error('newTagName')
                <p class="form-error">{{ $message }}</p>
            @enderror
            <div class="form-actions">
                <button class="btn-primary" type="submit">Criar</button>
                <button class="btn-link" type="button" wire:click="toggleTagForm">Cancelar</button>
            </div>
        </form>
    @endif

    <div class="tags">
        @forelse ($tags as $tag)
            <a class="tag" href="#" wire:key="tag-{{ $tag->id }}">
                <span class="dot" style="background:{{ $tag->color ?: '#ccc' }}"></span>
                <span>{{ $tag->name }}</span>
            </a>
        @empty
            <span class="muted">Sem tags disponíveis.</span>
        @endforelse
    </div>

    <h6 style="margin-top:14px"> </h6>
    <div class="completed">
        <i class="icon" data-lucide="check-square"></i>
        {{ $completedLabel }}
        <span class="count" style="margin-left:auto">{{ $completedCount }}</span>
    </div>
</aside>
