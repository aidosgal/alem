<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Exception;

class ServiceService
{
    protected ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Get all services for an organization.
     */
    public function getOrganizationServices(Organization $organization)
    {
        return $this->serviceRepository->getByOrganization($organization);
    }

    /**
     * Create a new service for an organization.
     */
    public function createService(array $data, Organization $organization): Service
    {
        $data['organization_id'] = $organization->id;
        
        return $this->serviceRepository->create($data);
    }

    /**
     * Update a service.
     */
    public function updateService(Service $service, array $data, Organization $organization): Service
    {
        if (!$this->serviceRepository->belongsToOrganization($service, $organization)) {
            throw new Exception('У вас нет прав для редактирования этой услуги.');
        }

        return $this->serviceRepository->update($service, $data);
    }

    /**
     * Delete a service.
     */
    public function deleteService(Service $service, Organization $organization): bool
    {
        if (!$this->serviceRepository->belongsToOrganization($service, $organization)) {
            throw new Exception('У вас нет прав для удаления этой услуги.');
        }

        return $this->serviceRepository->delete($service);
    }

    /**
     * Find service by ID.
     */
    public function findService(string $id): ?Service
    {
        return $this->serviceRepository->findById($id);
    }
}
