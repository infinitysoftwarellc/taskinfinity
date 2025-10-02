<?php

namespace Tests\Feature\Auth;

use App\Models\User;

it('renders the confirm password screen', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/confirm-password');

    $response
        ->assertOk()
        ->assertSeeText(__('Confirm your password'));
});

it('confirms the password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
});

it('rejects an invalid password confirmation', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($user)->from('/confirm-password')->post('/confirm-password', [
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/confirm-password');
});
