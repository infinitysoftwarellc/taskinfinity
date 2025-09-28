<div class="app">
    <livewire:tasks.rail
        :primary-buttons="data_get($rail, 'primary', [])"
        :secondary-buttons="data_get($rail, 'secondary', [])"
        :avatar-label="data_get($rail, 'avatarLabel', 'Você')"
    />

    <livewire:tasks.sidebar />

    <livewire:tasks.main-panel />

    <livewire:tasks.details />
</div>
