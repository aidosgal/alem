<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Collection;

class OrderStatusRepository
{
    /**
     * Get all statuses for an organization ordered by display order.
     */
    public function getByOrganization(Organization $organization): Collection
    {
        return OrderStatus::where('organization_id', $organization->id)
            ->withCount('orders')
            ->with('orders')
            ->orderBy('order')
            ->get();
    }

    /**
     * Find status by ID.
     */
    public function findById(string $id): ?OrderStatus
    {
        return OrderStatus::find($id);
    }

    /**
     * Create a new status.
     */
    public function create(array $data): OrderStatus
    {
        return OrderStatus::create($data);
    }

    /**
     * Update a status.
     */
    public function update(OrderStatus $status, array $data): OrderStatus
    {
        $status->update($data);
        return $status->fresh();
    }

    /**
     * Delete a status.
     */
    public function delete(OrderStatus $status): bool
    {
        return $status->delete();
    }

    /**
     * Create default statuses for an organization.
     */
    public function createDefaultStatuses(Organization $organization): Collection
    {
        $statuses = [];
        
        foreach (OrderStatus::getDefaultStatuses() as $statusData) {
            $statusData['organization_id'] = $organization->id;
            $statuses[] = $this->create($statusData);
        }

        return new Collection($statuses);
    }

    /**
     * Check if status belongs to organization.
     */
    public function belongsToOrganization(OrderStatus $status, Organization $organization): bool
    {
        return $status->organization_id === $organization->id;
    }
}
