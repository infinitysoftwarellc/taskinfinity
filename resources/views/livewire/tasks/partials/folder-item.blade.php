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

        <div class="ti-list-actions">
            <button
                type="button"
                class="ti-list-menu-button"
                wire:click.stop="toggleMenu('{{ $menuKey }}')"
                title="Mais opções"
            >
                <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
            </button>

            @if ($openMenuId === $menuKey)
                <div class="ti-dropdown" wire:click.away="closeMenu">
                    <button type="button" wire:click="openCreateModal('folder', {{ $folder->id }})">Editar</button>
                    <button type="button" wire:click="togglePinFolder({{ $folder->id }})">
                        {{ $folder->is_pinned ? 'Desafixar' : 'Fixar' }}
                    </button>
                    <button type="button" wire:click="duplicateFolder({{ $folder->id }})">Duplicar</button>
                    <button type="button" wire:click="toggleArchiveFolder({{ $folder->id }})">
                        {{ $folder->archived_at ? 'Desarquivar' : 'Arquivar' }}
                    </button>
                    <button type="button" class="is-danger" wire:click="deleteFolder({{ $folder->id }})">Deletar</button>
                </div>
            @endif
        </div>
    </div>

    <ul class="workspace-children">
        @forelse ($lists as $childList)
            @include('livewire.tasks.partials.list-item', [
                'list' => $childList,
                'currentListId' => $currentListId,
                'openMenuId' => $openMenuId,
            ])
        @empty
            <li class="workspace-empty">Nenhuma lista ainda.</li>
        @endforelse
    </ul>
</li>
