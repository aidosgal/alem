<?php

namespace App\Repositories;

use App\Models\Manager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagerRepository
{
    /**
     * Create a new manager with user account.
     */
    public function create(array $data): Manager
    {
        $user = User::create([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        return Manager::create([
            'user_id' => $user->id,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
        ]);
    }

    /**
     * Find manager by user email.
     */
    public function findByEmail(string $email): ?Manager
    {
        $user = User::where('email', $email)->first();
        return $user?->manager;
    }

    /**
     * Find manager by ID.
     */
    public function findById(string $id): ?Manager
    {
        return Manager::find($id);
    }

    /**
     * Update manager information.
     */
    public function update(Manager $manager, array $data): Manager
    {
        $manager->update($data);
        return $manager->fresh();
    }

    /**
     * Check if manager has any organization.
     */
    public function hasOrganization(Manager $manager): bool
    {
        return $manager->organizations()->exists();
    }

    /**
     * Get manager's organizations.
     */
    public function getOrganizations(Manager $manager)
    {
        return $manager->organizations;
    }
}
