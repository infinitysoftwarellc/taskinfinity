<?php

namespace App\Livewire\Support;

use Livewire\Component;

class NotificationDispatcher
{
    public function __construct(
        protected Component $component,
    ) {
    }

    public function success(string $title, ?string $description = null): void
    {
        $this->dispatch('success', $title, $description);
    }

    public function error(string $title, ?string $description = null): void
    {
        $this->dispatch('error', $title, $description);
    }

    public function warning(string $title, ?string $description = null): void
    {
        $this->dispatch('warning', $title, $description);
    }

    public function info(string $title, ?string $description = null): void
    {
        $this->dispatch('info', $title, $description);
    }

    protected function dispatch(string $type, string $title, ?string $description): void
    {
        $this->component->dispatch('app-notification', type: $type, title: $title, description: $description);
    }
}

trait InteractsWithNotifications
{
    protected function notification(): NotificationDispatcher
    {
        return new NotificationDispatcher($this);
    }
}
