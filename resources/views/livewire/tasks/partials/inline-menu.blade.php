<div class="ti-inline-menu" data-menu wire:click.stop>
    <button class="icon ghost" type="button" title="Mais opções" data-menu-trigger>
        <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
    </button>

    <div class="ti-inline-dropdown" role="dialog" aria-modal="true" aria-hidden="true">
        <button
            class="ti-inline-backdrop"
            type="button"
            aria-label="Fechar menu"
            data-menu-dismiss
        ></button>

        <div class="ti-inline-dialog" role="menu">
            @php
                $menuContext = $context ?? null;
                $menuMissionId = $missionId ?? null;
            @endphp
            @include('livewire.tasks.partials.menu-content', [
                'context' => $menuContext,
                'missionId' => $menuMissionId,
            ])
        </div>
    </div>
</div>
