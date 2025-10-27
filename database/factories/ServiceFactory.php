<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 50, 5000),
            'duration_days' => fake()->numberBetween(1, 30),
            'duration_max_days' => fake()->numberBetween(30, 90),
            'duration_min_days' => fake()->numberBetween(1, 10),
            'created_at' => now(),
        ];
    }

    public function quickService(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_days' => 1,
            'duration_min_days' => 1,
            'duration_max_days' => 3,
            'price' => fake()->randomFloat(2, 50, 500),
        ]);
    }

    public function longTermService(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_days' => 60,
            'duration_min_days' => 30,
            'duration_max_days' => 90,
            'price' => fake()->randomFloat(2, 1000, 10000),
        ]);
    }
}
