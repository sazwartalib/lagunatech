<?php

namespace Database\Factories;

use App\Models\Bug;
use App\Models\BugComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BugComment>
 */
class BugCommentFactory extends Factory
{
    protected $model = BugComment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bug_id' => Bug::factory(),
            'user_id' => User::factory(),
            'body' => fake()->sentence(fake()->numberBetween(6, 20)),
        ];
    }
}
