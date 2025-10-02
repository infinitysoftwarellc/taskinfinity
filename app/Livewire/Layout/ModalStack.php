<?php

namespace App\Livewire\Layout;

use Illuminate\Support\Str;
use Livewire\Component;

class ModalStack extends Component
{
    protected $listeners = [
        'openModal' => 'open',
        'closeModal' => 'close',
    ];

    /**
     * @var array<int, array{key:string, component:string, parameters:array}>
     */
    public array $modals = [];

    public function open(string $component, array $arguments = []): void
    {
        $this->modals[] = [
            'key' => (string) Str::uuid(),
            'component' => $component,
            'parameters' => $arguments,
        ];
    }

    public function close(?string $key = null): void
    {
        if ($key) {
            $this->modals = array_values(array_filter(
                $this->modals,
                fn (array $modal) => $modal['key'] !== $key
            ));

            return;
        }

        array_pop($this->modals);
    }

    public function render()
    {
        return view('livewire.layout.modal-stack');
    }
}
