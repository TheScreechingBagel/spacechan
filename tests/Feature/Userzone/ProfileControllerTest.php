<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('removes the thread and reply images of a deleted account from disk', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $thread = Thread::factory()->for($user)->create();
    $thread->addMedia(UploadedFile::fake()->image('thread.jpg'))->toMediaCollection('image');
    $reply = Post::factory()->for($user)->create();
    $reply->addMedia(UploadedFile::fake()->image('reply.jpg'))->toMediaCollection('image');
    $otherReplyInThread = Post::factory()->for($thread)->create();
    $otherReplyInThread->addMedia(UploadedFile::fake()->image('other.jpg'))->toMediaCollection('image');

    expect(Storage::disk('public')->allFiles())->not->toBeEmpty();

    $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password']);

    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('refuses to change the email to one outside the allowed domain', function () {
    config(['auth.allowed_email_domain' => 'student.harbour.space']);
    $user = User::factory()->create(['email' => 'me@student.harbour.space']);

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => 'me@example.com',
    ]);

    $response->assertSessionHasErrors('email');
    expect($user->fresh()->email)->toBe('me@student.harbour.space');
});
