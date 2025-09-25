<?php

namespace App\Livewire\Task;

use Livewire\Component;

class Sidebar extends Component
{
    public array $views = [
        ['label' => 'All', 'count' => 26, 'icon' => 'M3.75 5.25h16.5M3.75 9.75h16.5M3.75 14.25h16.5M3.75 18.75h16.5'],
        ['label' => 'Today', 'count' => 2, 'icon' => 'M6 4.5h12m-12 6.75h12M9.75 3v3m4.5-3v3M9 15.75l2.25 2.25L15 13.5'],
        ['label' => 'Next 7 Days', 'count' => 2, 'icon' => 'M8.25 6.75h7.5M8.25 12h7.5M8.25 17.25H12m-9.75-9l9-6 9 6v9a3 3 0 01-3 3h-12a3 3 0 01-3-3z'],
        ['label' => 'Inbox', 'count' => 3, 'icon' => 'M3.75 5.25h16.5a1.5 1.5 0 011.5 1.5v10.5a1.5 1.5 0 01-1.5 1.5h-16.5a1.5 1.5 0 01-1.5-1.5V6.75a1.5 1.5 0 011.5-1.5zm0 0L12 13.5l8.25-8.25'],
        ['label' => 'Summary', 'count' => 4, 'icon' => 'M4.5 6.75h15m-15 6h15M4.5 16.5h8.25'],
    ];

    public array $lists = [
        ['label' => 'SOFTWAREINFINITY', 'items' => 26],
        ['label' => 'teste', 'items' => 2],
        ['label' => 'Task Infinity', 'items' => 6],
        ['label' => 'PORTFOLIO', 'items' => 6],
    ];

    public string $filtersDescription = 'Display tasks filtered by list, date, priority, tag, and more.';

    public array $tags = [
        ['label' => 'PRÓXIMAS TASKS', 'color' => 'bg-sky-500'],
        ['label' => 'FOLDERS', 'color' => 'bg-purple-500'],
        ['label' => 'PORTFOLIO GUILHERMEINFINITY', 'color' => 'bg-amber-500'],
        ['label' => 'PORTFOLIO SOFTWAREINFINITY', 'color' => 'bg-lime-500'],
    ];

    public function render()
    {
        return view('livewire.task.sidebar');
    }
}
