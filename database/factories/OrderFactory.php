<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Order;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'organization_id' => Organization::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 100, 10000),
            'deadline_at' => fake()->dateTimeBetween('now', '+30 days'),
            'created_at' => now(),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function withServices(int $count = 1): static
    {
        return $this->afterCreating(function (Order $order) use ($count) {
            $services = \App\Models\Service::factory()
                ->count($count)
                ->create(['organization_id' => $order->organization_id]);

            foreach ($services as $service) {
                \App\Models\OrderService::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'status' => 'pending_funds',
                ]);
            }
        });
    }
}
