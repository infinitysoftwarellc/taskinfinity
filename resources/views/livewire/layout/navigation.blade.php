<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>
<div>
        <aside class="rail">
        <div class="avatar" title="Você"></div>
        <button class="btn" title="All"><i data-lucide="list-checks"></i></button>
        <button class="btn" title="Today"><i data-lucide="sun"></i></button>
        <button class="btn" title="7 Days"><i data-lucide="calendar-days"></i></button>
        <button class="btn" title="Inbox"><i data-lucide="inbox"></i></button>
        <button class="btn" title="Summary"><i data-lucide="pie-chart"></i></button>
        <div class="spacer"></div>
        <button class="btn" title="Settings"><i data-lucide="settings"></i></button>
    </aside>
</div>

