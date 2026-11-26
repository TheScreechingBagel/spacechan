<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;

it('shows a thread with its replies', function () {
    $thread = Thread::factory()->create(['text' => 'Opening post']);
    Post::factory()->for($thread)->create(['text' => 'First reply']);

    $response = $this->get(route('threads.show', $thread));

    $response->assertOk()
        ->assertSee('Opening post')
        ->assertSee('First reply');
});

it('hides the account name of the poster', function () {
    $user = User::factory()->create(['name' => 'Secret Account']);
    $thread = Thread::factory()->for($user)->create(['name' => null]);

    $response = $this->get(route('threads.show', $thread));

    $response->assertOk()
        ->assertSee('Anonymous')
        ->assertDontSee('Secret Account');
});

it('shows admins the account name of every poster', function () {
    $admin = User::factory()->admin()->create();
    $thread = Thread::factory()->create();
    Post::factory()->for($thread)->for(User::factory()->create(['name' => 'First Account']))->create();
    Post::factory()->for($thread)->for(User::factory()->create(['name' => 'Second Account']))->create();

    $response = $this->actingAs($admin)->get(route('threads.show', $thread));

    $response->assertOk()
        ->assertSee('First Account')
        ->assertSee('Second Account');
});

it('marks your own posts with (You)', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->for($user)->create();

    $response = $this->actingAs($user)->get(route('threads.show', $thread));

    $response->assertOk()->assertSee('(You)');
});
