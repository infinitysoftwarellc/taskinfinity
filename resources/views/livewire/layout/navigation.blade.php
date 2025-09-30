{{-- This Blade view renders the livewire layout navigation interface. --}}
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
        <button class="btn" title="All"><i class="fa-solid fa-list-check" aria-hidden="true"></i></button>
        <button class="btn" title="Today"><i class="fa-solid fa-sun" aria-hidden="true"></i></button>
        <button class="btn" title="7 Days"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></button>
        <button class="btn" title="Inbox"><i class="fa-solid fa-inbox" aria-hidden="true"></i></button>
        <button class="btn" title="Summary"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i></button>
        <div class="spacer"></div>
        <button class="btn" title="Settings"><i class="fa-solid fa-gear" aria-hidden="true"></i></button>
    </aside>
</div>

