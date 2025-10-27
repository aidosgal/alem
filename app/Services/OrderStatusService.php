<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrderStatus;
use App\Repositories\OrderStatusRepository;
use Exception;

class OrderStatusService
{
    protected OrderStatusRepository $orderStatusRepository;

    public function __construct(OrderStatusRepository $orderStatusRepository)
    {
        $this->orderStatusRepository = $orderStatusRepository;
    }

    /**
     * Get all statuses for an organization.
     */
    public function getOrganizationStatuses(Organization $organization)
    {
        return $this->orderStatusRepository->getByOrganization($organization);
    }

    /**
     * Create default statuses for a new organization.
     */
    public function createDefaultStatuses(Organization $organization)
    {
        return $this->orderStatusRepository->createDefaultStatuses($organization);
    }

    /**
     * Create a custom status for an organization.
     */
    public function createStatus(Organization $organization, array $data): OrderStatus
    {
        $data['organization_id'] = $organization->id;
        $data['is_default'] = false;
        
        // Generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']) . '_' . time();
        }

        return $this->orderStatusRepository->create($data);
    }

    /**
     * Update a status.
     */
    public function updateStatus(string $id, Organization $organization, array $data): OrderStatus
    {
        $status = $this->orderStatusRepository->findById($id);
        
        if (!$status) {
            throw new Exception('Статус не найден.');
        }
        
        if (!$this->orderStatusRepository->belongsToOrganization($status, $organization)) {
            throw new Exception('У вас нет прав для редактирования этого статуса.');
        }

        return $this->orderStatusRepository->update($status, $data);
    }

    /**
     * Delete a status.
     */
    public function deleteStatus(string $id, Organization $organization): bool
    {
        $status = $this->orderStatusRepository->findById($id);
        
        if (!$status) {
            throw new Exception('Статус не найден.');
        }
        
        if (!$this->orderStatusRepository->belongsToOrganization($status, $organization)) {
            throw new Exception('У вас нет прав для удаления этого статуса.');
        }

        if ($status->orders()->exists()) {
            throw new Exception('Невозможно удалить статус, к которому привязаны заказы.');
        }

        return $this->orderStatusRepository->delete($status);
    }

    /**
     * Get status by ID for organization.
     */
    public function getStatusById(string $id, Organization $organization): ?OrderStatus
    {
        $status = $this->orderStatusRepository->findById($id);
        
        if ($status && $this->orderStatusRepository->belongsToOrganization($status, $organization)) {
            return $status;
        }
        
        return null;
    }
}
