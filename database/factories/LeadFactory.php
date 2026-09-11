<?php

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        return [
            'reference' => sprintf('LEAD-%s-%s', now()->year, str_pad((string) $counter, 3, '0', STR_PAD_LEFT)),
            'name' => fake()->name(),
            'company' => fake()->boolean(80) ? fake()->company().' Sdn Bhd' : null,
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('01#-### ####'),
            'project_type' => fake()->randomElement(['Web Application', 'Mobile Application', 'Custom System', 'Website', 'Not sure yet']),
            'budget_range' => fake()->randomElement(['Below RM 10k', 'RM 10k – 30k', 'RM 30k – 60k', 'RM 60k+', 'Not sure']),
            'message' => fake()->paragraph(),
            'source' => fake()->randomElement(LeadSource::cases()),
            'status' => fake()->randomElement(LeadStatus::cases()),
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function statusNew(): static
    {
        return $this->state(fn () => ['status' => LeadStatus::New]);
    }

    public function fromWebsite(): static
    {
        return $this->state(fn () => ['source' => LeadSource::Website]);
    }
}
