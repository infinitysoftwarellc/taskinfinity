<?php

namespace App\Livewire\Settings\TwoFactor;

use Exception;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RecoveryCodes extends Component
{
    #[Locked]
    public array $recoveryCodes = [];

    public bool $requiresConfirmation = false;

    public function render()
    {
        return view('livewire.settings.two-factor.recovery-codes');
    }

    public function mount(bool $requiresConfirmation = false): void
    {
        $this->requiresConfirmation = $requiresConfirmation;
        $this->loadRecoveryCodes();
    }

    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generateNewRecoveryCodes): void
    {
        $generateNewRecoveryCodes(auth()->user());

        $this->loadRecoveryCodes();
    }

    private function loadRecoveryCodes(): void
    {
        $user = auth()->user();

        if ($user->hasEnabledTwoFactorAuthentication() && $user->two_factor_recovery_codes) {
            try {
                $this->recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
            } catch (Exception) {
                $this->addError('recoveryCodes', __('Failed to load recovery codes'));

                $this->recoveryCodes = [];
            }
        }
    }
}
