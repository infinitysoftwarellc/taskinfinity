{{-- This Blade view renders the livewire tasks partials folder item interface. --}}
@php
    $menuKey = 'folder-' . $folder->id;
    $lists = $folder->lists ?? collect();
    $totalCount = $lists->sum('missions_count');
    $color = $folder->color ?: '#7aa2ff';
@endphp

<li class="workspace-item is-folder" wire:key="workspace-folder-{{ $folder->id }}">
    <div class="workspace-row">
        <div class="nav-item nav-folder">
            <span class="color-dot" style="background: {{ $color }}"></span>
            <span class="label">{{ $folder->name }}</span>
            <span class="count">{{ $totalCount }}</span>
        </div>

        <div
            class="ti-list-actions"
            x-data="tiInlineMenu({ placement: 'left-start', offset: 6 })"
            x-id="['folder-menu-' . $folder->id, 'folder-trigger-' . $folder->id]"
        >
            <button
                type="button"
                class="ti-list-menu-button"
                x-ref="trigger"
                :id="$id('folder-trigger-' . $folder->id)"
                :aria-controls="$id('folder-menu-' . $folder->id)"
                :aria-expanded="open.toString()"
                aria-haspopup="true"
                title="Mais opções"
                @click.prevent="toggle()"
            >
                <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                <span class="sr-only">Abrir menu de pasta</span>
            </button>

            <div
                class="ti-dropdown"
                x-ref="dropdown"
                x-show="open"
                x-transition.origin.top.right
                role="menu"
                :id="$id('folder-menu-' . $folder->id)"
                :aria-labelledby="$id('folder-trigger-' . $folder->id)"
                :aria-hidden="(!open).toString()"
                @keydown.escape.stop.prevent="close(true)"
                @click.outside="close()"
                @click="if ($event.target.closest('button')) close(true)"
            >
                <button type="button" wire:click="openCreateModal('folder', {{ $folder->id }})" role="menuitem">Editar</button>
                <button type="button" wire:click="togglePinFolder({{ $folder->id }})" role="menuitem">
                    {{ $folder->is_pinned ? 'Desafixar' : 'Fixar' }}
                </button>
                <button type="button" wire:click="duplicateFolder({{ $folder->id }})" role="menuitem">Duplicar</button>
                <button type="button" wire:click="toggleArchiveFolder({{ $folder->id }})" role="menuitem">
                    {{ $folder->archived_at ? 'Desarquivar' : 'Arquivar' }}
                </button>
                <button type="button" class="is-danger" wire:click="deleteFolder({{ $folder->id }})" role="menuitem">Deletar</button>
            </div>
        </div>
    </div>

    <ul class="workspace-children">
        @forelse ($lists as $childList)
            @include('livewire.tasks.partials.list-item', [
                'list' => $childList,
                'currentListId' => $currentListId,
            ])
        @empty
            <li class="workspace-empty">Nenhuma lista ainda.</li>
        @endforelse
    </ul>
</li>
