<?php

namespace Database\Factories;

use App\Enums\LeadAttachmentKind;
use App\Models\Lead;
use App\Models\LeadAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadAttachment>
 */
class LeadAttachmentFactory extends Factory
{
    protected $model = LeadAttachment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'kind' => LeadAttachmentKind::Attachment,
            'disk' => 'local',
            'path' => 'leads/'.fake()->uuid().'.pdf',
            'original_name' => fake()->word().'.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1_000, 2_000_000),
        ];
    }

    public function logo(): static
    {
        return $this->state(fn () => [
            'kind' => LeadAttachmentKind::Logo,
            'original_name' => 'logo.png',
            'mime_type' => 'image/png',
        ]);
    }
}
