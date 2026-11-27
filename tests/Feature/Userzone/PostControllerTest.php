<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('sends guests to the login page when they reply', function () {
    $thread = Thread::factory()->create();

    $response = $this->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'Hello',
    ]);

    $response->assertRedirect(route('login'));
    expect(Post::count())->toBe(0);
});

it('saves a reply owned by the poster', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create();

    $response = $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'Hello',
    ]);

    $response->assertRedirect(route('threads.show', $thread));
    $this->assertDatabaseHas('posts', [
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'text' => 'Hello',
    ]);
});

it('stores an image with the reply', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $thread = Thread::factory()->create();

    $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'With a picture',
        'image' => UploadedFile::fake()->image('photo.jpg'),
    ]);

    expect(Post::sole()->hasMedia('image'))->toBeTrue();
});

it('rejects a reply upload that is not an image', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create();

    $response = $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'With a file',
        'image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors(['image']);
    expect(Post::count())->toBe(0);
});

it('rejects a reply image with a web page file name', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $thread = Thread::factory()->create();

    // a real JPEG under an .html name, which a browser would open as a web page
    $photo = UploadedFile::fake()->image('photo.jpg');
    $disguisedImage = new UploadedFile($photo->getPathname(), 'page.html', test: true);

    $response = $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'With a disguised file',
        'image' => $disguisedImage,
    ]);

    $response->assertSessionHasErrors(['image']);
    expect(Post::count())->toBe(0);
});

it('bumps the thread when someone replies', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create(['bumped_at' => '2026-01-01 00:00:00']);
    $this->travelTo('2026-06-01 12:00:00');

    $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'Bump',
    ]);

    expect($thread->fresh()->bumped_at->toDateTimeString())->toBe('2026-06-01 12:00:00');
});

it('rejects a reply without text', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create();

    $response = $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => '',
    ]);

    $response->assertSessionHasErrors(['text']);
    expect(Post::count())->toBe(0);
});

it('lets the owner delete their reply', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    $response = $this->actingAs($user)->delete(route('userzone.posts.destroy', $post));

    $response->assertRedirect(route('threads.show', $post->thread_id));
    $this->assertModelMissing($post);
});

it('lets an admin delete any reply', function () {
    $admin = User::factory()->admin()->create();
    $post = Post::factory()->create();

    $this->actingAs($admin)->delete(route('userzone.posts.destroy', $post));

    $this->assertModelMissing($post);
});

it('refuses to delete the reply of another user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->delete(route('userzone.posts.destroy', $post));

    $response->assertForbidden();
    $this->assertModelExists($post);
});

it('slows down a user who posts too many replies in a minute', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create();

    foreach (range(1, 10) as $i) {
        $this->actingAs($user)->post(route('userzone.posts.store'), [
            'thread_id' => $thread->id,
            'text' => 'Reply '.$i,
        ]);
    }

    $response = $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'One too many',
    ]);

    $response->assertTooManyRequests();
    expect(Post::count())->toBe(10);
});

it('asks a user to verify their email before replying', function () {
    $user = User::factory()->unverified()->create();
    $thread = Thread::factory()->create();

    $response = $this->actingAs($user)->post(route('userzone.posts.store'), [
        'thread_id' => $thread->id,
        'text' => 'Hello',
    ]);

    $response->assertRedirect(route('verification.notice'));
    expect(Post::count())->toBe(0);
});
