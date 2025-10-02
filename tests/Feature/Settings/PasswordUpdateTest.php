<?php

use App\Livewire\Settings\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('updates the account password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    Livewire::actingAs($user)
        ->test(Password::class)
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

it('requires the current password to change it', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    Livewire::actingAs($user)
        ->test(Password::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);
});
