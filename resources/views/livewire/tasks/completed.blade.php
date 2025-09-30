{{-- This Blade view renders the livewire tasks completed interface. --}}
<div class="app completed-view">
    <livewire:tasks.rail
        :primary-buttons="data_get($rail, 'primary', [])"
        :secondary-buttons="data_get($rail, 'secondary', [])"
        :avatar-label="data_get($rail, 'avatarLabel', 'Você')"
    />

    <livewire:tasks.sidebar :current-list-id="null" :current-shortcut="null" :completed-view="true" />

    <main class="main panel">
        <div class="toolbar">
            <div class="title">
                Completed
                <span class="bubble">{{ $totalCount }}</span>
            </div>
            <div class="spacer"></div>
        </div>

        @if ($groups->isEmpty())
            <div class="empty-state">
                <p>Nenhuma tarefa concluída ainda.</p>
            </div>
        @else
            <div class="completed-groups">
                @foreach ($groups as $index => $group)
                    <section class="group completed-group" aria-expanded="true" wire:key="completed-group-{{ $index }}">
                        <header class="group-header">
                            <span class="group-title">{{ $group['label'] }}</span>
                            <span class="group-count">{{ count($group['missions']) }}</span>
                        </header>
                        <div class="group-body">
                            <ul class="completed-list">
                                @foreach ($group['missions'] as $missionIndex => $mission)
                                    <li class="completed-item" wire:key="completed-mission-{{ $index }}-{{ $missionIndex }}-{{ $mission['id'] }}">
                                        <div class="completed-item-main">
                                            <span class="completed-item-title">{{ $mission['title'] }}</span>
                                            @if ($mission['completed_time'])
                                                <span
                                                    class="completed-item-time"
                                                    title="{{ optional($mission['completed_at'])->format('d/m/Y H:i') }}"
                                                >
                                                    {{ $mission['completed_time'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="completed-item-meta">
                                            @if ($mission['list'])
                                                <span class="completed-item-list">Lista: {{ $mission['list'] }}</span>
                                            @else
                                                <span class="completed-item-list is-muted">Sem lista</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </main>
</div>
