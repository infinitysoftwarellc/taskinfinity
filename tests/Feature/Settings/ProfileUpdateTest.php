<?php

use App\Livewire\Settings\DeleteUserForm;
use App\Livewire\Settings\Profile;
use App\Models\User;
use Livewire\Livewire;

it('shows the settings profile page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertOk();
});

it('updates the profile from settings', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

it('keeps verification when the email is unchanged', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

it('deletes the user from settings', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect(User::find($user->id))->toBeNull();
    expect(auth()->check())->toBeFalse();
});

it('requires the correct password before deletion', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(DeleteUserForm::class)
        ->set('password', 'wrong-password')
        ->call('deleteUser')
        ->assertHasErrors(['password']);

    expect(User::find($user->id))->not->toBeNull();
});
