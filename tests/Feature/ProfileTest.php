<?php

use App\Livewire\Profile\DeleteUserForm;
use App\Livewire\Profile\UpdatePasswordForm;
use App\Livewire\Profile\UpdateProfileInformationForm;
use App\Models\User;
use Livewire\Livewire;

it('displays the profile page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/profile');

    $response
        ->assertOk()
        ->assertSeeText(__('Profile Information'))
        ->assertSeeText(__('Update Password'))
        ->assertSeeText(__('Delete Account'));
});

it('updates profile information', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(UpdateProfileInformationForm::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

it('keeps verification when email does not change', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(UpdateProfileInformationForm::class)
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not()->toBeNull();
});

it('deletes the account with correct password', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect(User::find($user->id))->toBeNull();
});

it('requires the correct password to delete the account', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(DeleteUserForm::class)
        ->set('password', 'wrong-password')
        ->call('deleteUser')
        ->assertHasErrors(['password']);

    expect(User::find($user->id))->not()->toBeNull();
});
