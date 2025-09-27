<?php

use App\Models\User;

test('non-admin users are redirected from admin analytics', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user);

    $response = $this->get(route('admin.analytics'));

    $response->assertRedirect(route('dashboard'));
});

test('non-admin users are redirected from the admin dashboard routes', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user);

    $response = $this->get(route('dashboard.index'));

    $response->assertRedirect(route('dashboard'));
});
