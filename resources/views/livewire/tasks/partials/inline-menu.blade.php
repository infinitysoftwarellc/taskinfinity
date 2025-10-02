{{-- This Blade view renders the livewire tasks partials inline menu interface. --}}
<div
    class="ti-inline-menu"
    x-data="tiInlineMenu({ placement: 'bottom-end' })"
    x-id="['inline-menu','inline-trigger']"
    wire:click.stop
>
    <button
        class="icon ghost"
        type="button"
        title="Mais opções"
        x-ref="trigger"
        :id="$id('inline-trigger')"
        :aria-controls="$id('inline-menu')"
        :aria-expanded="open.toString()"
        aria-haspopup="true"
        @click.prevent="toggle()"
    >
        <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
        <span class="sr-only">Abrir menu contextual</span>
    </button>

    <div
        class="ti-inline-dropdown"
        x-ref="dropdown"
        x-show="open"
        x-transition.origin.top.right
        role="menu"
        :id="$id('inline-menu')"
        :aria-labelledby="$id('inline-trigger')"
        :aria-hidden="(!open).toString()"
        @keydown.escape.stop.prevent="close(true)"
        @click.outside="close()"
    >
        <button
            class="ti-inline-backdrop"
            type="button"
            aria-label="Fechar menu"
            @click.prevent="close()"
        ></button>

        <div
            class="ti-inline-dialog"
            role="none"
            @click="if ($event.target.closest('[data-menu-item]')) close(true)"
        >
            @php
                $menuContext = $context ?? null;
                $menuMissionId = $missionId ?? null;
                $menuSubtaskId = $subtaskId ?? null;
            @endphp
            @php
                $menuPriority = $priority ?? null;
            @endphp
            @include('livewire.tasks.partials.menu-content', [
                'context' => $menuContext,
                'missionId' => $menuMissionId,
                'subtaskId' => $menuSubtaskId,
                'priority' => $menuPriority,
            ])
        </div>
    </div>
</div>
