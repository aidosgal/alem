<?php

namespace Database\Factories;

use App\Models\Applicant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant>
 */
class ApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'created_at' => now(),
        ];
    }

    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => \App\Models\User::factory(),
        ]);
    }

    public function withBalance(float $amount = 1000.00): static
    {
        return $this->afterCreating(function (Applicant $applicant) use ($amount) {
            \App\Models\ApplicantBalance::create([
                'applicant_id' => $applicant->id,
                'amount' => $amount,
            ]);
        });
    }
}
