<?php

use App\Models\Thread;
use App\Models\User;

it('lets an admin edit a thread', function () {
    $admin = User::factory()->admin()->create();
    $thread = Thread::factory()->create();

    $response = $this->actingAs($admin)->put(route('admin.threads.update', $thread), [
        'subject' => 'Moderated',
        'text' => 'Cleaned up text',
    ]);

    $response->assertRedirect(route('threads.show', $thread));
    expect($thread->fresh())
        ->subject->toBe('Moderated')
        ->text->toBe('Cleaned up text');
});

it('refuses to let a user who is not an admin edit a thread', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->for($user)->create(['text' => 'Original']);

    $response = $this->actingAs($user)->put(route('admin.threads.update', $thread), [
        'text' => 'Changed',
    ]);

    $response->assertForbidden();
    expect($thread->fresh()->text)->toBe('Original');
});

it('rejects a thread edit without text', function () {
    $admin = User::factory()->admin()->create();
    $thread = Thread::factory()->create(['text' => 'Original']);

    $response = $this->actingAs($admin)->put(route('admin.threads.update', $thread), [
        'text' => '',
    ]);

    $response->assertSessionHasErrors(['text']);
    expect($thread->fresh()->text)->toBe('Original');
});
