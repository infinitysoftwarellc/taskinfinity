<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('non admin users cannot access the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertForbidden();
});

test('admins can visit the dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $response = $this->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertSeeText(__('Admin Dashboard'))
        ->assertSeeText(__('Usuários cadastrados'));
});
