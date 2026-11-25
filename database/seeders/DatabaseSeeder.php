<?php

namespace Database\Seeders;

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

        $threads = Thread::factory(15)->recycle($users)->create();

        foreach ($threads as $thread) {
            $replies = Post::factory(fake()->numberBetween(0, 8))
                ->recycle($users)
                ->for($thread)
                ->create();

            if ($replies->isNotEmpty()) {
                $thread->update(['bumped_at' => $replies->max('created_at')]);
            }
        }
    }
}
