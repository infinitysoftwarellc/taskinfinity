<div class="app">
    <livewire:tasks.rail
        :primary-buttons="data_get($rail, 'primary', [])"
        :secondary-buttons="data_get($rail, 'secondary', [])"
        :avatar-label="data_get($rail, 'avatarLabel', 'Você')"
    />

    <livewire:tasks.sidebar
        :shortcuts="data_get($sidebar, 'shortcuts', [])"
        :workspace="data_get($sidebar, 'workspace', [])"
        :filters-tip="data_get($sidebar, 'filtersTip', '')"
        :tags="data_get($sidebar, 'tags', [])"
        :completed-label="data_get($sidebar, 'completedLabel', 'Completed')"
    />

    <livewire:tasks.main-panel :panel="$panel" />

    <livewire:tasks.details :details="$details" />
</div>
