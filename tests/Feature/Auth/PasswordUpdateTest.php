<?php

namespace Tests\Feature\Auth;

use App\Livewire\Profile\UpdatePasswordForm;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('updates the password', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(UpdatePasswordForm::class)
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

it('requires the current password', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(UpdatePasswordForm::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);
});
