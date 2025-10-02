<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

it('renders the forgot password screen', function () {
    $response = $this->get('/forgot-password');

    $response
        ->assertOk()
        ->assertSeeText(__('Forgot your password?'));
});

it('sends a reset link', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('renders the reset password screen', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) {
        $response = $this->get('/reset-password/'.$notification->token.'?email=test@example.com');

        $response
            ->assertOk()
            ->assertSeeText(__('Reset your password'));

        return true;
    });
});

it('resets the password with a valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertCredentials([
            'email' => $user->email,
            'password' => 'new-password',
        ]);

        return true;
    });
});
