<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

/**
 * Componente que representa a barra lateral (rail) da página de tarefas.
 */
class Rail extends Component
{
    /**
     * Botões principais (filtros/atalhos) exibidos no rail.
     */
    public array $primaryButtons = [];

    /**
     * Botões secundários (configurações, etc.) exibidos no rail.
     */
    public array $secondaryButtons = [];

    /**
     * Texto do avatar/resumo do usuário.
     */
    public string $avatarLabel = 'Você';

    /**
     * Preenche as propriedades com os dados vindos da board.
     */
    public function mount(
        array $primaryButtons = [],
        array $secondaryButtons = [],
        string $avatarLabel = 'Você'
    ): void {
        $this->primaryButtons = $primaryButtons ?: [
            ['icon' => 'list-checks', 'title' => 'All'],
            ['icon' => 'sun', 'title' => 'Today'],
            ['icon' => 'calendar-days', 'title' => '7 Days'],
            ['icon' => 'inbox', 'title' => 'Inbox'],
            ['icon' => 'pie-chart', 'title' => 'Summary'],
        ];

        $this->secondaryButtons = $secondaryButtons ?: [
            ['icon' => 'settings', 'title' => 'Settings'],
        ];

        $this->avatarLabel = $avatarLabel;
    }

    /**
     * Retorna a view responsável por desenhar o rail na página de tarefas.
     */
    public function render()
    {
        return view('livewire.tasks.rail');
    }
}
