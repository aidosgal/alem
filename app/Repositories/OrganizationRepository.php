<?php

namespace App\Repositories;

use App\Models\Manager;
use App\Models\Organization;
use App\Models\OrganizationUsers;

class OrganizationRepository
{
    /**
     * Create a new organization.
     */
    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    /**
     * Find organization by ID.
     */
    public function findById(string $id): ?Organization
    {
        return Organization::find($id);
    }

    /**
     * Find organization by code or invitation token.
     */
    public function findByCode(string $code): ?Organization
    {
        // This can be extended later with invitation codes
        return Organization::where('id', $code)->first();
    }

    /**
     * Attach manager to organization.
     */
    public function attachManager(Organization $organization, Manager $manager): void
    {
        if (!$organization->managers()->where('manager_id', $manager->id)->exists()) {
            $organization->managers()->attach($manager->id);
        }
    }

    /**
     * Detach manager from organization.
     */
    public function detachManager(Organization $organization, Manager $manager): void
    {
        $organization->managers()->detach($manager->id);
    }

    /**
     * Check if manager belongs to organization.
     */
    public function managerBelongsToOrganization(Organization $organization, Manager $manager): bool
    {
        return $organization->managers()->where('manager_id', $manager->id)->exists();
    }

    /**
     * Get all organizations for a manager.
     */
    public function getManagerOrganizations(Manager $manager)
    {
        return $manager->organizations;
    }

    /**
     * Update organization information.
     */
    public function update(Organization $organization, array $data): Organization
    {
        $organization->update($data);
        return $organization->fresh();
    }
}
