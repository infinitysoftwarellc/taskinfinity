{{-- This Blade view renders the livewire tasks partials list item interface. --}}
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

        <div
            class="ti-list-actions"
            x-data="tiInlineMenu({ placement: 'left-start', offset: 6 })"
            x-id="['list-menu-' . $list->id, 'list-trigger-' . $list->id]"
        >
            <button
                type="button"
                class="ti-list-menu-button"
                x-ref="trigger"
                :id="$id('list-trigger-' . $list->id)"
                :aria-controls="$id('list-menu-' . $list->id)"
                :aria-expanded="open.toString()"
                aria-haspopup="true"
                title="Mais opções"
                @click.prevent="toggle()"
            >
                <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                <span class="sr-only">Abrir menu de lista</span>
            </button>

            <div
                class="ti-dropdown"
                x-ref="dropdown"
                x-show="open"
                x-transition.origin.top.right
                role="menu"
                :id="$id('list-menu-' . $list->id)"
                :aria-labelledby="$id('list-trigger-' . $list->id)"
                :aria-hidden="(!open).toString()"
                @keydown.escape.stop.prevent="close(true)"
                @click.outside="close()"
                @click="if ($event.target.closest('button')) close(true)"
            >
                <button type="button" wire:click="openCreateModal('list', {{ $list->id }})" role="menuitem">Editar</button>
                <button type="button" wire:click="togglePin({{ $list->id }})" role="menuitem">
                    {{ $list->is_pinned ? 'Desafixar' : 'Fixar' }}
                </button>
                <button type="button" wire:click="duplicateList({{ $list->id }})" role="menuitem">Duplicar</button>
                <button type="button" wire:click="toggleArchive({{ $list->id }})" role="menuitem">
                    {{ $list->archived_at ? 'Desarquivar' : 'Arquivar' }}
                </button>
                <button type="button" class="is-danger" wire:click="deleteList({{ $list->id }})" role="menuitem">Deletar</button>
            </div>
        </div>
    </div>
</li>
