@php
    $menuKey = 'list-' . $list->id;
    $isActive = $currentListId === $list->id;
    $color = $list->color ?: '#7aa2ff';
@endphp

<li class="workspace-item is-list {{ $isActive ? 'is-active' : '' }}" wire:key="workspace-list-{{ $list->id }}">
    <div class="workspace-row">
        <a
            wire:navigate
            class="nav-item nav-list-item {{ $isActive ? 'is-active' : '' }}"
            href="{{ route('tasks.index', ['taskList' => $list->id]) }}"
        >
            <span class="color-dot" style="background: {{ $color }}"></span>
            <span class="label">{{ $list->name }}</span>
            <span class="count">{{ $list->missions_count }}</span>
        </a>

        <div class="ti-list-actions">
            <button
                type="button"
                class="ti-list-menu-button"
                wire:click.stop="toggleMenu('{{ $menuKey }}')"
                title="Mais opções"
            >
                <i data-lucide="more-horizontal"></i>
            </button>

            @if ($openMenuId === $menuKey)
                <div class="ti-dropdown" wire:click.away="closeMenu">
                    <button type="button" wire:click="openCreateModal('list', {{ $list->id }})">Editar</button>
                    <button type="button" wire:click="togglePin({{ $list->id }})">
                        {{ $list->is_pinned ? 'Desafixar' : 'Fixar' }}
                    </button>
                    <button type="button" wire:click="duplicateList({{ $list->id }})">Duplicar</button>
                    <button type="button" wire:click="toggleArchive({{ $list->id }})">
                        {{ $list->archived_at ? 'Desarquivar' : 'Arquivar' }}
                    </button>
                    <button type="button" class="is-danger" wire:click="deleteList({{ $list->id }})">Deletar</button>
                </div>
            @endif
        </div>
    </div>
</li>
