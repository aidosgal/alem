<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class ManagerOrderApiController extends Controller
{
    protected OrderService $orderService;
    protected OrderStatusService $orderStatusService;

    public function __construct(
        OrderService $orderService,
        OrderStatusService $orderStatusService
    ) {
        $this->orderService = $orderService;
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * Get all orders for the authenticated manager's organization.
     * GET /api/manager/orders
     */
    public function index(Request $request)
    {
        try {
            $manager = $request->user()->manager;
            
            if (!$manager) {
                return response()->json(['error' => 'Менеджер не найден'], 404);
            }

            $organization = $manager->currentOrganization();
            
            if (!$organization) {
                return response()->json(['error' => 'Организация не найдена'], 404);
            }

            $kanban = $this->orderService->getOrdersForKanban($organization);

            return response()->json([
                'success' => true,
                'data' => array_values($kanban),
            ]);
        } catch (\Exception $e) {
            \Log::error('API: Failed to fetch orders: ' . $e->getMessage());
            return response()->json(['error' => 'Не удалось загрузить заказы'], 500);
        }
    }

    /**
     * Get order statuses for the organization.
     * GET /api/manager/order-statuses
     */
    public function getStatuses(Request $request)
    {
        try {
            $manager = $request->user()->manager;
            
            if (!$manager) {
                return response()->json(['error' => 'Менеджер не найден'], 404);
            }

            $organization = $manager->currentOrganization();
            
            if (!$organization) {
                return response()->json(['error' => 'Организация не найдена'], 404);
            }

            $statuses = $this->orderStatusService->getOrganizationStatuses($organization);

            return response()->json([
                'success' => true,
                'data' => $statuses,
            ]);
        } catch (\Exception $e) {
            \Log::error('API: Failed to fetch statuses: ' . $e->getMessage());
            return response()->json(['error' => 'Не удалось загрузить статусы'], 500);
        }
    }

    /**
     * Update order status.
     * PATCH /api/manager/orders/{id}/status
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $manager = $request->user()->manager;
            
            if (!$manager) {
                return response()->json(['error' => 'Менеджер не найден'], 404);
            }

            $organization = $manager->currentOrganization();
            
            if (!$organization) {
                return response()->json(['error' => 'Организация не найдена'], 404);
            }

            $request->validate([
                'status_id' => 'required|string',
            ]);

            $order = $this->orderService->findOrder($id);
            
            if (!$order) {
                return response()->json(['error' => 'Заказ не найден'], 404);
            }

            $updatedOrder = $this->orderService->updateOrderStatus(
                $order,
                $request->input('status_id'),
                $organization
            );

            return response()->json([
                'success' => true,
                'data' => $updatedOrder->load(['applicant.user', 'orderStatus']),
            ]);
        } catch (\Exception $e) {
            \Log::error('API: Failed to update order status: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
