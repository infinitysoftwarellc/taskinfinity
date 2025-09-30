{{-- This Blade view renders the livewire tasks sidebar interface. --}}
@php
    $workspaceStyle = $workspaceExpanded ? '' : 'display:none;';
    $iconMap = [
        'list-checks' => 'fa-solid fa-list-check',
        'sun' => 'fa-solid fa-sun',
        'calendar-days' => 'fa-solid fa-calendar-days',
        'inbox' => 'fa-solid fa-inbox',
        'pie-chart' => 'fa-solid fa-chart-pie',
        'chevron-down' => 'fa-solid fa-chevron-down',
        'plus' => 'fa-solid fa-plus',
        'check-square' => 'fa-solid fa-square-check',
        'x' => 'fa-solid fa-xmark',
        'folder' => 'fa-solid fa-folder',
        'list' => 'fa-solid fa-list',
    ];
    $resolveIcon = fn ($name) => $iconMap[$name] ?? 'fa-solid fa-circle';
@endphp

{{-- ELEMENTO RAIZ ÚNICO --}}
<div class="sidebar-container">
    <aside class="sidebar panel">
        <h6>Atalhos</h6>
        <nav>
            <ul class="nav-list">
                @forelse ($shortcuts as $index => $shortcut)
                    <li wire:key="shortcut-{{ $index }}">
                        <a
                            wire:navigate
                            class="nav-item {{ ($shortcut['active'] ?? false) ? 'is-active' : '' }}"
                            href="{{ $shortcut['href'] ?? '#' }}"
                        >
                            <i class="icon {{ $resolveIcon($shortcut['icon'] ?? '') }}" aria-hidden="true"></i>
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

        <div class="workspace" aria-expanded="{{ $workspaceExpanded ? 'true' : 'false' }}">
            <button
                class="workspace-toggle"
                type="button"
                data-toggle="workspace"
                aria-expanded="{{ $workspaceExpanded ? 'true' : 'false' }}"
            >
                <i class="chev fa-solid fa-chevron-down" aria-hidden="true"></i>
                <span class="title">{{ $workspaceTitle ?? 'Listas' }}</span>
                @if ($workspaceBadge)
                    <span class="badge">{{ $workspaceBadge }}</span>
                @endif
            </button>
        </div>

        <div class="workspace-content" style="{{ $workspaceStyle }}">
            <ul class="nav-list workspace-tree">
                @foreach ($folders as $folder)
                    @include('livewire.tasks.partials.folder-item', [
                        'folder' => $folder,
                        'currentListId' => $currentListId,
                        'openMenuId' => $openMenuId,
                    ])
                @endforeach

                @foreach ($standaloneLists as $list)
                    @include('livewire.tasks.partials.list-item', [
                        'list' => $list,
                        'currentListId' => $currentListId,
                        'openMenuId' => $openMenuId,
                    ])
                @endforeach

                @if ($folders->isEmpty() && $standaloneLists->isEmpty())
                    <li class="muted">Nenhuma lista ou pasta criada ainda.</li>
                @endif

            </ul>
        </div>

        <div class="sidebar-section-header">
            <h6>Tags</h6>
            <button class="btn-new" type="button" title="Nova tag" wire:click.prevent="openTagModal">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
            </button>
        </div>

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
        <a
            wire:navigate
            class="completed {{ ($completedActive ?? false) ? 'is-active' : '' }}"
            href="{{ $completedHref ?? '#' }}"
        >
            <i class="icon fa-solid fa-square-check" aria-hidden="true"></i>
            <span class="label">{{ $completedLabel }}</span>
            @if (($completedCount ?? 0) > 0)
                <span class="count">{{ $completedCount }}</span>
            @endif
        </a>
    </aside>

    {{-- MODAL DENTRO DO MESMO ELEMENTO RAIZ --}}
    @if ($showCreateModal)
        <div class="ti-modal-backdrop" wire:click="closeCreateModal">
            <div
                class="ti-modal"
                wire:click.stop
                wire:keydown.escape.window="closeCreateModal"
                tabindex="-1"
            >
                <form class="ti-modal-form" wire:submit.prevent="saveList">
                    <header class="ti-modal-header">
                        <div>
                            <h3>{{ $editingListId || $editingFolderId ? 'Editar lista ou pasta' : 'Nova lista ou pasta' }}</h3>
                            <p>Organize tudo com nome, visualização, pasta e cor.</p>
                        </div>
                        <button class="ti-modal-close" type="button" wire:click="closeCreateModal">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </button>
                    </header>

                    <div class="ti-modal-body">
                        <div class="ti-modal-row">
                            <label class="ti-modal-label" for="list-name">Nome</label>
                            <input
                                id="list-name"
                                type="text"
                                class="ti-modal-control"
                                placeholder="Ex.: Planejamento"
                                wire:model.defer="newListName"
                                autofocus
                            />
                            @error('newListName')
                                <p class="ti-modal-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ti-modal-row">
                            <span class="ti-modal-label">Tipo</span>
                            <div class="ti-type-toggle">
                                <button
                                    type="button"
                                    class="ti-type-option {{ $newListType === 'list' ? 'is-active' : '' }}"
                                    wire:click="$set('newListType', 'list')"
                                    {{ $editingFolderId ? 'disabled' : '' }}
                                >
                                    <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                                    Lista
                                </button>
                                <button
                                    type="button"
                                    class="ti-type-option {{ $newListType === 'folder' ? 'is-active' : '' }}"
                                    wire:click="$set('newListType', 'folder')"
                                    {{ $editingListId ? 'disabled' : '' }}
                                >
                                    <i class="fa-solid fa-folder" aria-hidden="true"></i>
                                    Pasta
                                </button>
                            </div>
                            @error('newListType')
                                <p class="ti-modal-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ti-modal-row">
                            <label class="ti-modal-label" for="list-view-type">Tipo de visualização</label>
                            <select
                                id="list-view-type"
                                class="ti-modal-control"
                                wire:model="newListViewType"
                                {{ $newListType === 'folder' ? 'disabled' : '' }}
                            >
                                @foreach ($viewTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="ti-modal-hint">Visualização é aplicada somente às listas.</p>
                        </div>

                        <div class="ti-modal-row">
                            <label class="ti-modal-label" for="list-folder">Pasta</label>
                            <select
                                id="list-folder"
                                class="ti-modal-control"
                                wire:model="newListFolder"
                                {{ $newListType === 'folder' ? 'disabled' : '' }}
                            >
                                <option value="">Sem pasta</option>
                                @foreach ($folderOptions as $folderOption)
                                    <option value="{{ $folderOption->id }}">{{ $folderOption->name }}</option>
                                @endforeach
                            </select>
                            @error('newListFolder')
                                <p class="ti-modal-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ti-modal-row">
                            <span class="ti-modal-label">Cor</span>
                            <div class="ti-color-grid">
                                @foreach ($colorPalette as $color)
                                    <button
                                        type="button"
                                        class="ti-color-swatch {{ $newListColor === $color ? 'is-selected' : '' }}"
                                        style="--swatch-color: {{ $color }}"
                                        wire:click="$set('newListColor', '{{ $color }}')"
                                    ></button>
                                @endforeach
                                <label class="ti-color-custom">
                                    <input class="ti-color-input" type="color" wire:model="newListColor" />
                                    <span class="ti-color-preview" style="--swatch-color: {{ $newListColor }}"></span>
                                    <span class="ti-color-label">Custom</span>
                                </label>
                            </div>
                            @error('newListColor')
                                <p class="ti-modal-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <footer class="ti-modal-actions">
                        <button class="btn-primary" type="submit">
                            {{ $editingListId || $editingFolderId ? 'Salvar alterações' : 'Criar' }}
                        </button>
                        <button class="btn-link" type="button" wire:click="closeCreateModal">Cancelar</button>
                    </footer>
                </form>
            </div>
        </div>
    @endif

    @if ($showTagModal)
        <div class="ti-modal-backdrop" wire:click="closeTagModal">
            <div
                class="ti-modal"
                wire:click.stop
                wire:keydown.escape.window="closeTagModal"
                tabindex="-1"
            >
                <form class="ti-modal-form" wire:submit.prevent="createTag">
                    <header class="ti-modal-header">
                        <div>
                            <h3>Nova tag</h3>
                            <p>Categorize missões rapidamente com nome e cor.</p>
                        </div>
                        <button class="ti-modal-close" type="button" wire:click="closeTagModal">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </button>
                    </header>

                    <div class="ti-modal-body">
                        <div class="ti-modal-row">
                            <label class="ti-modal-label" for="tag-name">Nome</label>
                            <input
                                id="tag-name"
                                type="text"
                                class="ti-modal-control"
                                placeholder="Ex.: Prioridade alta"
                                wire:model.defer="newTagName"
                                autofocus
                            />
                            @error('newTagName')
                                <p class="ti-modal-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="ti-modal-row">
                            <span class="ti-modal-label">Cor</span>
                            <div class="ti-color-grid">
                                @foreach ($colorPalette as $color)
                                    <button
                                        type="button"
                                        class="ti-color-swatch {{ $newTagColor === $color ? 'is-selected' : '' }}"
                                        style="--swatch-color: {{ $color }}"
                                        wire:click="$set('newTagColor', '{{ $color }}')"
                                    ></button>
                                @endforeach
                                <label class="ti-color-custom">
                                    <input class="ti-color-input" type="color" wire:model="newTagColor" />
                                    <span class="ti-color-preview" style="--swatch-color: {{ $newTagColor }}"></span>
                                    <span class="ti-color-label">Custom</span>
                                </label>
                            </div>
                            @error('newTagColor')
                                <p class="ti-modal-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <footer class="ti-modal-actions">
                        <button class="btn-primary" type="submit">Criar tag</button>
                        <button class="btn-link" type="button" wire:click="closeTagModal">Cancelar</button>
                    </footer>
                </form>
            </div>
        </div>
    @endif
</div>
