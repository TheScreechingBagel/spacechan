<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->optional(0.3)->name(),
            'text' => $this->faker->paragraph(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'thread_id' => Thread::factory(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * attach one of the sample photos in database/factories/images.
     * preservingOriginal() copies the photo so the sample file stays where it is
     */
    public function withImage(): static
    {
        return $this->afterCreating(function (Post $post) {
            $post->addMedia(fake()->randomElement(glob(database_path('factories/images/*.jpg'))))
                ->preservingOriginal()
                ->toMediaCollection('image');
        });
    }
}
