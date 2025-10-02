{{-- This Blade view renders the livewire tasks board interface. --}}
<div class="app {{ $isListView ? 'list-view' : '' }}">
    <livewire:tasks.rail
        :primary-buttons="data_get($rail, 'primary', [])"
        :secondary-buttons="data_get($rail, 'secondary', [])"
        :avatar-label="data_get($rail, 'avatarLabel', 'Você')"
    />

    <livewire:tasks.sidebar :current-list-id="$listId" :current-shortcut="$shortcut" />

    <livewire:tasks.main-panel
        :current-list-id="$listId"
        :shortcut="$shortcut"
        :initial-mission-id="$initialMissionId"
    />

    <livewire:tasks.details :initial-mission-id="$initialMissionId" />
</div>
