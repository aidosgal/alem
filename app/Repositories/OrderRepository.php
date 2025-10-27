<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Organization;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * Get all orders for an organization grouped by status.
     */
    public function getByOrganizationGroupedByStatus(Organization $organization): Collection
    {
        return Order::where('organization_id', $organization->id)
            ->with(['applicant.user', 'orderStatus'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get orders by organization and status.
     */
    public function getByOrganizationAndStatus(Organization $organization, OrderStatus $status): Collection
    {
        return Order::where('organization_id', $organization->id)
            ->where('status_id', $status->id)
            ->with(['applicant.user', 'orderStatus'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find order by ID.
     */
    public function findById(string $id): ?Order
    {
        return Order::with(['applicant.user', 'orderStatus'])->find($id);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $order->update(['status_id' => $status->id]);
        return $order->fresh(['applicant.user', 'orderStatus']);
    }

    /**
     * Check if order belongs to organization.
     */
    public function belongsToOrganization(Order $order, Organization $organization): bool
    {
        return $order->organization_id === $organization->id;
    }
}
