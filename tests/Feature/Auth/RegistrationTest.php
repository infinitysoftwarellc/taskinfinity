<?php

namespace Tests\Feature\Auth;

use App\Models\User;

it('renders the registration screen', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeText(__('Create an account'));
});

it('registers new users', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    $this->assertDatabaseHas(User::class, [
        'email' => 'test@example.com',
    ]);
});
