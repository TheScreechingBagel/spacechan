<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(8)->create();

        $users = User::all();

        Board::factory()->create(['slug' => 'hs', 'title' => 'Harbour Space', 'description' => 'Campus life, classes and teachers.']);
        Board::factory()->create(['slug' => 'g', 'title' => 'Technology', 'description' => 'Code, computers and gadgets.']);
        Board::factory()->create(['slug' => 'b', 'title' => 'Random', 'description' => 'Anything else.']);

        $boards = Board::all();

        Thread::factory(12)->withImage()->recycle($boards)->recycle($users)->create();
        Thread::factory(3)->recycle($boards)->recycle($users)->create();
        $threads = Thread::all();

        foreach ($threads as $thread) {
            Post::factory(fake()->numberBetween(0, 6))->recycle($users)->for($thread)->create();
            Post::factory(fake()->numberBetween(0, 2))->withImage()->recycle($users)->for($thread)->create();

            $lastReplyAt = $thread->posts()->max('created_at');

            if ($lastReplyAt) {
                $thread->update(['bumped_at' => $lastReplyAt]);
            }
        }
    }
}
