<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ThreadFactory extends Factory
{
    protected $model = Thread::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->optional(0.3)->name(),
            'subject' => $this->faker->optional(0.7)->sentence(3),
            'text' => $this->faker->paragraph(),
            'bumped_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'board_id' => Board::factory(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * attach one of the sample photos in database/factories/images.
     * preservingOriginal() copies the photo so the sample file stays where it is
     */
    public function withImage(): static
    {
        return $this->afterCreating(function (Thread $thread) {
            $thread->addMedia(fake()->randomElement(glob(database_path('factories/images/*.jpg'))))
                ->preservingOriginal()
                ->toMediaCollection('image');
        });
    }
}
