<?php

use App\Models\Board;
use App\Models\User;

it('returns to the previous page after login', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create();

    $this->get(route('login'), ['referer' => route('boards.show', $board)]);
    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('boards.show', $board));
});

it('does not send the user to another website after login', function () {
    $user = User::factory()->create();

    $this->get(route('login'), ['referer' => 'https://evil.example/']);
    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
});
