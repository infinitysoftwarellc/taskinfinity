<?php

use App\Models\User;

it('renders the login screen', function () {
    $response = $this->get('/login');

    $response
        ->assertOk()
        ->assertSeeText(__('Log in to your account'));
});

it('authenticates users via the login form', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $this->assertGuest();
});

it('displays the navigation shell for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response
        ->assertOk()
        ->assertSeeText(__('Dashboard'))
        ->assertSeeText(__('Tasks'));
});

it('logs out the user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});
