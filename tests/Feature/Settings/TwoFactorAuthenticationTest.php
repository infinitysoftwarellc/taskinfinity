<?php

use App\Livewire\Settings\TwoFactor;
use App\Models\User;
use Laravel\Fortify\Features;
use Livewire\Livewire;

beforeEach(function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);
});

it('renders the two-factor settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'))
        ->assertOk()
        ->assertSee('Two Factor Authentication')
        ->assertSee('Disabled');
});

it('requires password confirmation when navigating directly', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('two-factor.show'))
        ->assertRedirect(route('password.confirm'));
});

it('forbids access when two-factor is disabled globally', function () {
    config(['fortify.features' => []]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('two-factor.show'))
        ->assertForbidden();
});

it('resets pending confirmation data when the process is abandoned', function () {
    $user = User::factory()->create();

    $user->forceFill([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => null,
    ])->save();

    Livewire::actingAs($user)
        ->test(TwoFactor::class)
        ->assertSet('twoFactorEnabled', false);

    expect($user->refresh()->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
});
