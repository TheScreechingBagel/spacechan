<?php

use App\Models\Board;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('sends guests to the login page to start a thread', function () {
    $board = Board::factory()->create();

    $response = $this->get(route('userzone.boards.threads.create', $board));

    $response->assertRedirect(route('login'));
});

it('starts a thread on a board owned by the poster', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create();

    $response = $this->actingAs($user)->post(route('userzone.boards.threads.store', $board), [
        'subject' => 'Hello',
        'text' => 'First post',
    ]);

    $thread = Thread::sole();
    $response->assertRedirect(route('threads.show', $thread));
    expect($thread->board_id)->toBe($board->id)
        ->and($thread->user_id)->toBe($user->id)
        ->and($thread->text)->toBe('First post');
});

it('stores an image with the thread', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $board = Board::factory()->create();

    $this->actingAs($user)->post(route('userzone.boards.threads.store', $board), [
        'text' => 'With a picture',
        'image' => UploadedFile::fake()->image('photo.jpg'),
    ]);

    expect(Thread::sole()->hasMedia('image'))->toBeTrue();
});

it('rejects a thread without text', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create();

    $response = $this->actingAs($user)->post(route('userzone.boards.threads.store', $board), [
        'text' => '',
    ]);

    $response->assertSessionHasErrors(['text']);
    expect(Thread::count())->toBe(0);
});

it('rejects an upload that is not an image', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create();

    $response = $this->actingAs($user)->post(route('userzone.boards.threads.store', $board), [
        'text' => 'With a file',
        'image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors(['image']);
    expect(Thread::count())->toBe(0);
});

it('lets the owner delete their thread and its replies', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->for($user)->create();
    $reply = Post::factory()->for($thread)->create();

    $response = $this->actingAs($user)->delete(route('userzone.threads.destroy', $thread));

    $response->assertRedirect(route('boards.show', $thread->board));
    $this->assertModelMissing($thread);
    $this->assertModelMissing($reply);
});

it('removes the images of the replies along with the thread', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $thread = Thread::factory()->for($user)->create();
    $reply = Post::factory()->for($thread)->create();
    $media = $reply->addMedia(UploadedFile::fake()->image('photo.jpg'))->toMediaCollection('image');

    $this->actingAs($user)->delete(route('userzone.threads.destroy', $thread));

    $this->assertModelMissing($media);
    Storage::disk('public')->assertMissing($media->getPathRelativeToRoot());
});

it('lets an admin delete any thread', function () {
    $admin = User::factory()->admin()->create();
    $thread = Thread::factory()->create();

    $this->actingAs($admin)->delete(route('userzone.threads.destroy', $thread));

    $this->assertModelMissing($thread);
});

it('refuses to delete the thread of another user', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->create();

    $response = $this->actingAs($user)->delete(route('userzone.threads.destroy', $thread));

    $response->assertForbidden();
    $this->assertModelExists($thread);
});
