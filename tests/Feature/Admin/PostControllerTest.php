<?php

use App\Models\Post;
use App\Models\User;

it('lets an admin edit a reply', function () {
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($admin)->put(route('admin.posts.update', $post), [
        'text' => 'Cleaned up text',
    ]);

    $response->assertRedirect(route('threads.show', $post->thread_id));
    expect($post->fresh()->text)->toBe('Cleaned up text');
});

it('refuses to let a user who is not an admin edit a reply', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create(['text' => 'Original']);

    $response = $this->actingAs($user)->put(route('admin.posts.update', $post), [
        'text' => 'Changed',
    ]);

    $response->assertForbidden();
    expect($post->fresh()->text)->toBe('Original');
});

it('rejects a reply edit without text', function () {
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create(['text' => 'Original']);

    $response = $this->actingAs($admin)->put(route('admin.posts.update', $post), [
        'text' => '',
    ]);

    $response->assertSessionHasErrors(['text']);
    expect($post->fresh()->text)->toBe('Original');
});
