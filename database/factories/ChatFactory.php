<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Chat;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    protected $model = Chat::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'applicant_id' => Applicant::factory(),
            'created_at' => now(),
            'last_message_text' => null,
            'last_message_at' => null,
            'last_sender' => null,
        ];
    }
}
