<?php

namespace Database\Factories;

use App\Models\Manager;
use App\Models\Organization;
use App\Models\OrganizationUsers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrganizatinUsersFactory extends Factory
{
    protected $model = OrganizationUsers::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'manager_id' => Manager::factory(),
            'created_at' => now(),
        ];
    }
}
