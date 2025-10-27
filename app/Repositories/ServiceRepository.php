<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository
{
    /**
     * Get all services for an organization.
     */
    public function getByOrganization(Organization $organization): Collection
    {
        return Service::where('organization_id', $organization->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find service by ID.
     */
    public function findById(string $id): ?Service
    {
        return Service::find($id);
    }

    /**
     * Create a new service.
     */
    public function create(array $data): Service
    {
        return Service::create($data);
    }

    /**
     * Update a service.
     */
    public function update(Service $service, array $data): Service
    {
        $service->update($data);
        return $service->fresh();
    }

    /**
     * Delete a service.
     */
    public function delete(Service $service): bool
    {
        return $service->delete();
    }

    /**
     * Check if service belongs to organization.
     */
    public function belongsToOrganization(Service $service, Organization $organization): bool
    {
        return $service->organization_id === $organization->id;
    }
}
