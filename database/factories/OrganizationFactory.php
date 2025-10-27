<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'image' => fake()->imageUrl(640, 480, 'business'),
            'description' => fake()->paragraph(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'registration_document' => fake()->url() . '/registration.pdf',
            'authority_document' => fake()->url() . '/authority.pdf',
            'created_at' => now(),
        ];
    }

    public function withBalance(float $amount = 5000.00): static
    {
        return $this->afterCreating(function (Organization $organization) use ($amount) {
            \App\Models\OrganizationBalance::create([
                'organization_id' => $organization->id,
                'amount' => $amount,
            ]);
        });
    }

    public function withManager(): static
    {
        return $this->afterCreating(function (Organization $organization) {
            $manager = \App\Models\Manager::factory()->create();
            \App\Models\OrganizationUsers::create([
                'organization_id' => $organization->id,
                'manager_id' => $manager->id,
            ]);
        });
    }
}
