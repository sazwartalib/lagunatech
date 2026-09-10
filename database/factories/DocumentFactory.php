<?php

namespace Database\Factories;

use App\Enums\DocumentCategory;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 *
 * Note: these rows point at placeholder paths; no real file is written by the
 * factory. The upload action is what actually persists files to storage.
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['requirements', 'wireframes', 'er-diagram', 'deployment-guide', 'meeting-notes', 'contract']).'-'.fake()->numerify('##').'.pdf';

        return [
            'project_id' => Project::factory(),
            'uploaded_by' => User::factory(),
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(DocumentCategory::cases()),
            'disk' => 'local',
            'path' => 'documents/demo/'.$name,
            'original_name' => $name,
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(50_000, 5_000_000),
            'is_internal' => fake()->boolean(60),
        ];
    }

    public function shared(): static
    {
        return $this->state(fn () => ['is_internal' => false]);
    }
}
