<?php

use App\Models\Board;
use App\Models\Thread;
use App\Models\User;

it('lists the boards for admins', function () {
    $admin = User::factory()->admin()->create();
    Board::factory()->create(['slug' => 'g']);

    $response = $this->actingAs($admin)->get(route('admin.boards.index'));

    $response->assertOk()->assertSee('/g/');
});

it('refuses the board pages to users who are not admins', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.boards.index'));

    $response->assertForbidden();
});

it('creates a board', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.boards.store'), [
        'slug' => 'mu',
        'title' => 'Music',
    ]);

    $response->assertRedirect(route('admin.boards.index'));
    $this->assertDatabaseHas('boards', ['slug' => 'mu', 'title' => 'Music']);
});

it('rejects a slug that is already taken', function () {
    $admin = User::factory()->admin()->create();
    Board::factory()->create(['slug' => 'g']);

    $response = $this->actingAs($admin)->post(route('admin.boards.store'), [
        'slug' => 'g',
        'title' => 'Another g',
    ]);

    $response->assertSessionHasErrors(['slug']);
    expect(Board::count())->toBe(1);
});

it('rejects a slug that does not fit in a url', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.boards.store'), [
        'slug' => 'Not OK',
        'title' => 'Bad slug',
    ]);

    $response->assertSessionHasErrors(['slug']);
    expect(Board::count())->toBe(0);
});

it('updates a board and keeps its own slug', function () {
    $admin = User::factory()->admin()->create();
    $board = Board::factory()->create(['slug' => 'g', 'title' => 'Old']);

    $response = $this->actingAs($admin)->put(route('admin.boards.update', $board), [
        'slug' => 'g',
        'title' => 'New',
    ]);

    $response->assertRedirect(route('admin.boards.index'));
    expect($board->fresh()->title)->toBe('New');
});

it('deletes an empty board', function () {
    $admin = User::factory()->admin()->create();
    $board = Board::factory()->create();

    $response = $this->actingAs($admin)->delete(route('admin.boards.destroy', $board));

    $response->assertRedirect(route('admin.boards.index'));
    $this->assertModelMissing($board);
});

it('refuses to delete a board that still has threads', function () {
    $admin = User::factory()->admin()->create();
    $board = Board::factory()->create();
    Thread::factory()->for($board)->create();

    $response = $this->actingAs($admin)->delete(route('admin.boards.destroy', $board));

    $response->assertRedirect(route('admin.boards.index'))->assertSessionHas('error');
    $this->assertModelExists($board);
});
