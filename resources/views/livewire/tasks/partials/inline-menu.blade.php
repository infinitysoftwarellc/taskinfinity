<div class="ti-inline-menu" data-menu wire:click.stop>
    <button class="icon ghost" type="button" title="Mais opções" data-menu-trigger>
        <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
    </button>

    <div class="ti-inline-dropdown" role="menu">
        @include('livewire.tasks.partials.menu-content')
    </div>
</div>
