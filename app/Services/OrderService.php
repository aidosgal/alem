<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Organization;
use App\Models\OrderStatus;
use App\Repositories\OrderRepository;
use App\Repositories\OrderStatusRepository;
use Exception;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected OrderStatusRepository $orderStatusRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderStatusRepository $orderStatusRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderStatusRepository = $orderStatusRepository;
    }

    /**
     * Get all orders for an organization.
     */
    public function getOrganizationOrders(Organization $organization)
    {
        return $this->orderRepository->getByOrganizationGroupedByStatus($organization);
    }

    /**
     * Get orders grouped by status for Kanban display.
     */
    public function getOrdersForKanban(Organization $organization): array
    {
        $statuses = $this->orderStatusRepository->getByOrganization($organization);
        $orders = $this->orderRepository->getByOrganizationGroupedByStatus($organization);

        $kanban = [];
        foreach ($statuses as $status) {
            $kanban[$status->id] = [
                'status' => $status,
                'orders' => $orders->where('status_id', $status->id)->values(),
            ];
        }

        return $kanban;
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(Order $order, string $newStatusId, Organization $organization): Order
    {
        if (!$this->orderRepository->belongsToOrganization($order, $organization)) {
            throw new Exception('У вас нет прав для изменения этого заказа.');
        }

        $newStatus = $this->orderStatusRepository->findById($newStatusId);
        
        if (!$newStatus || !$this->orderStatusRepository->belongsToOrganization($newStatus, $organization)) {
            throw new Exception('Статус не найден.');
        }

        return $this->orderRepository->updateStatus($order, $newStatus);
    }

    /**
     * Find order by ID.
     */
    public function findOrder(string $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }
}
