<?php

use App\Models\Board;
use App\Models\Thread;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('lists the boards on the front page', function () {
    Board::factory()->create(['slug' => 'g', 'title' => 'Technology']);

    $response = $this->get(route('boards.index'));

    $response->assertOk()->assertSee('/g/ - Technology');
});

it('shows the threads of a board', function () {
    $board = Board::factory()->create();
    Thread::factory()->for($board)->create(['subject' => 'On this board']);
    Thread::factory()->create(['subject' => 'On another board']);

    $response = $this->get(route('boards.show', $board));

    $response->assertOk()
        ->assertSee('On this board')
        ->assertDontSee('On another board');
});

it('shows a thumbnail for every thread with an image', function () {
    Storage::fake('public');
    $board = Board::factory()->create();
    $threads = Thread::factory(2)->for($board)->create();
    foreach ($threads as $thread) {
        $thread->addMedia(UploadedFile::fake()->image('photo.jpg'))->toMediaCollection('image');
    }

    $response = $this->get(route('boards.show', $board));

    $response->assertOk()
        ->assertSee($threads[0]->getFirstMediaUrl('image', 'thumb'))
        ->assertSee($threads[1]->getFirstMediaUrl('image', 'thumb'));
});
