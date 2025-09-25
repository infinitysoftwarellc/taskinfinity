<?php

namespace App\Livewire\Task;

use Livewire\Component;

class Sidebar extends Component
{
    public array $filters = [
        ['label' => 'Today', 'count' => 26, 'icon' => 'M3.75 12h16.5m-16.5 4.5h16.5M3.75 7.5h16.5M5.25 3.75l.41 1.23a1.125 1.125 0 001.07.77h10.5a1.125 1.125 0 001.07-.77l.41-1.23'],
        ['label' => 'Next 7 Days', 'count' => 11, 'icon' => 'M12 6v6h4.5m4.5 0A9 9 0 1112 3a9 9 0 019 9z'],
        ['label' => 'Inbox', 'count' => 8, 'icon' => 'M4.5 4.5h15a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6a1.5 1.5 0 011.5-1.5zm0 0L12 12l7.5-7.5'],
        ['label' => 'Summary', 'count' => 3, 'icon' => 'M3 8.25A2.25 2.25 0 015.25 6h13.5A2.25 2.25 0 0121 8.25v7.5A2.25 2.25 0 0118.75 18H5.25A2.25 2.25 0 013 15.75v-7.5z'],
    ];

    public array $folders = [
        ['label' => 'SOFTWAREINFINITY', 'items' => 5],
        ['label' => 'teste', 'items' => 2],
        ['label' => 'Task Infinity', 'items' => 4],
        ['label' => 'PORTFOLIO', 'items' => 3],
    ];

    public array $labels = [
        ['label' => 'PRÓXIMAS TASKS', 'color' => 'bg-sky-500'],
        ['label' => 'FOLDERS', 'color' => 'bg-purple-500'],
        ['label' => 'PORTFOLIO GUILHERMEINFINITY', 'color' => 'bg-amber-500'],
        ['label' => 'PORTFOLIO SOFTWAREINFINITY', 'color' => 'bg-lime-500'],
    ];

    public array $filtersSecondary = [
        ['label' => 'Completed', 'count' => 5],
        ['label' => 'Archived', 'count' => 12],
    ];

    public function render()
    {
        return view('livewire.task.sidebar');
    }
}
