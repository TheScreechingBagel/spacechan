<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

it('lets anyone register when no email domain is configured', function () {
    config(['auth.allowed_email_domain' => null]);

    $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
});

it('lets a user register with the allowed email domain', function () {
    config(['auth.allowed_email_domain' => 'student.harbour.space']);

    $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@student.harbour.space',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
});

it('refuses to register an email outside the allowed domain', function () {
    config(['auth.allowed_email_domain' => 'student.harbour.space']);

    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
    expect(User::count())->toBe(0);
});

it('sends a verification email to a new user', function () {
    Notification::fake();

    $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Notification::assertSentTo(User::sole(), VerifyEmail::class);
});
