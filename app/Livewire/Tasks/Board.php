<?php

namespace App\Livewire\Tasks;

use App\Support\MissionShortcutFilter;
use Livewire\Component;

/**
 * Componente responsável por renderizar a "board" da página de tarefas.
 * Ele costura o painel principal, a barra lateral e os atalhos de filtro.
 */
class Board extends Component
{
    /**
     * Estrutura da barra lateral exibida na board.
     */
    public array $rail = [];

    /**
     * Identificador da lista atual (quando a página é filtrada por uma lista).
     */
    public ?int $listId = null;

    /**
     * Indica se a board está sendo exibida em contexto de lista específica.
     */
    public bool $isListView = false;

    /**
     * Atalho selecionado (Hoje, 7 dias, etc.).
     */
    public ?string $shortcut = null;

    /**
     * Missão que deve ser destacada ao carregar a board (quando vindo do Spotlight).
     */
    public ?int $initialMissionId = null;

    /**
     * Inicializa o componente com possíveis filtros de lista e atalho.
     */
    public function mount(?int $listId = null, ?string $shortcut = null, ?int $initialMissionId = null): void
    {
        $this->listId = $listId;
        $this->isListView = $listId !== null;
        $this->initialMissionId = $initialMissionId;

        if ($shortcut && in_array($shortcut, MissionShortcutFilter::supported(), true)) {
            $this->shortcut = $shortcut;
        }

        if ($this->isListView) {
            $this->shortcut = null;
        }

        $this->rail = [
            'avatarLabel' => 'Você',
            'primary' => [
                ['icon' => 'list-checks', 'title' => 'All'],
                ['icon' => 'sun', 'title' => 'Today'],
                ['icon' => 'calendar-days', 'title' => '7 Days'],
                ['icon' => 'inbox', 'title' => 'Inbox'],
                ['icon' => 'pie-chart', 'title' => 'Summary'],
            ],
            'secondary' => [
                ['icon' => 'settings', 'title' => 'Settings'],
            ],
        ];
    }

    /**
     * Renderiza a board conectando com a view Blade da página de tarefas.
     */
    public function render()
    {
        return view('livewire.tasks.board');
    }
}
