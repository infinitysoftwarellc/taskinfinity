<div class="tasks-sidebar">
    <div class="tasks-sidebar-section">
        <h3 class="tasks-section-title">{{ __('Visões rápidas') }}</h3>

        <ul class="tasks-nav-list">
            @foreach ($views as $quickView)
                <li>
                    <button
                        type="button"
                        class="tasks-nav-btn {{ ($activeView === $quickView['slug']) ? 'is-active' : '' }}"
                        wire:click="openView('{{ $quickView['slug'] }}')"
                        wire:key="tasks-view-{{ $quickView['slug'] }}"
                    >
                        <span class="tasks-nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $quickView['icon'] }}" />
                            </svg>
                        </span>
                        <span>{{ __($quickView['label']) }}</span>
                        <span class="tasks-nav-count">{{ number_format($quickView['count']) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tasks-sidebar-section" wire:key="tasks-sidebar-lists">
        <div class="tasks-section-title">{{ __('Listas') }}</div>

        <button type="button" class="tasks-sidebar-action" wire:click="$toggle('showCreateList')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>{{ $showCreateList ? __('Cancelar') : __('Nova lista') }}</span>
        </button>

        @if ($showCreateList)
            <form wire:submit.prevent="createList" class="tasks-create-list">
                <div class="tasks-field">
                    <label for="list-name">{{ __('Nome') }}</label>
                    <input
                        id="list-name"
                        type="text"
                        class="tasks-input"
                        wire:model.defer="form.name"
                        placeholder="{{ __('Nome da lista') }}"
                    />
                    @error('form.name')
                        <span class="tasks-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="tasks-field">
                    <label for="list-view-mode">{{ __('Visualização padrão') }}</label>
                    <select id="list-view-mode" class="tasks-select" wire:model.defer="form.view_mode">
                        <option value="list">{{ __('Lista') }}</option>
                        <option value="kanban">{{ __('Kanban') }}</option>
                        <option value="timeline">{{ __('Linha do tempo') }}</option>
                    </select>
                    @error('form.view_mode')
                        <span class="tasks-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="tasks-create-actions">
                    <button type="button" class="tasks-button-secondary" wire:click="$toggle('showCreateList')">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="tasks-button-primary">
                        {{ __('Criar lista') }}
                    </button>
                </div>
            </form>
        @endif

        <ul class="tasks-nav-list">
            @forelse ($lists as $item)
                <li>
                    <button
                        type="button"
                        class="tasks-nav-btn {{ ($activeListId === $item->id) ? 'is-active' : '' }}"
                        wire:click="openList({{ $item->id }})"
                        wire:key="tasks-list-{{ $item->id }}"
                    >
                        <span class="tasks-nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15m-15 5.25h15m-15 5.25h15" />
                            </svg>
                        </span>
                        <span>{{ $item->name }}</span>
                        <span class="tasks-nav-count">{{ number_format($item->tasks_count) }}</span>
                    </button>
                </li>
            @empty
                <li class="tasks-sidebar-empty">
                    {{ __('Nenhuma lista cadastrada ainda.') }}
                </li>
            @endforelse
        </ul>
    </div>

    <div class="tasks-sidebar-section">
        <div class="tasks-section-title">{{ __('Filtros') }}</div>
        <div class="tasks-info-card">
            {{ $filtersDescription }}
        </div>
    </div>

    <div class="tasks-sidebar-section">
        <div class="tasks-section-title">{{ __('Tags') }}</div>

        @if (empty($tags))
            <div class="tasks-sidebar-empty">{{ __('Nenhuma tag criada ainda.') }}</div>
        @else
            <div class="tasks-tags">
                @foreach ($tags as $tag)
                    <div class="tasks-tag" wire:key="tasks-tag-{{ $tag['id'] }}">
                        <span class="tasks-tag-dot" style="background: {{ $tag['color'] }};"></span>
                        <span>{{ $tag['label'] }}</span>
                        <span class="tasks-nav-count">{{ number_format($tag['tasks_count']) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>