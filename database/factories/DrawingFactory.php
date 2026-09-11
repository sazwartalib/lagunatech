<?php

namespace Database\Factories;

use App\Enums\DrawingStatus;
use App\Models\Drawing;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Drawing>
 */
class DrawingFactory extends Factory
{
    protected $model = Drawing::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $creator = User::factory();

        return [
            'drawable_type' => Project::class,
            'drawable_id' => Project::factory(),
            'title' => fake()->randomElement(['Homepage Wireframe', 'Dashboard Flow', 'User Journey', 'Database Structure', 'Final UI Concept']),
            'description' => fake()->optional()->sentence(),
            'canvas_data' => ['version' => '6.0.0', 'objects' => []],
            'status' => fake()->randomElement(DrawingStatus::cases()),
            'created_by' => $creator,
            'updated_by' => $creator,
        ];
    }

    public function forDrawable(Model $drawable): static
    {
        return $this->state(fn () => [
            'drawable_type' => $drawable::class,
            'drawable_id' => $drawable->getKey(),
        ]);
    }
}
