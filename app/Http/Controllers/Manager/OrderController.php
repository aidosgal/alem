<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\OrganizationService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected AuthService $authService;
    protected OrganizationService $organizationService;
    protected OrderService $orderService;

    public function __construct(
        AuthService $authService,
        OrganizationService $organizationService,
        OrderService $orderService
    ) {
        $this->authService = $authService;
        $this->organizationService = $organizationService;
        $this->orderService = $orderService;
    }

    /**
     * Display Kanban board with orders.
     */
    public function index()
    {
        $manager = $this->authService->getAuthenticatedManager();
        $currentOrganization = $this->organizationService->getCurrentOrganization($manager);
        
        if (!$currentOrganization) {
            return redirect()->route('manager.organization.select')
                ->with('error', 'Пожалуйста, выберите организацию.');
        }

        $kanban = $this->orderService->getOrdersForKanban($currentOrganization);

        return view('manager.orders.index', compact('kanban', 'currentOrganization'));
    }

    /**
     * Update order status (AJAX endpoint for drag and drop).
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $manager = $this->authService->getAuthenticatedManager();
            $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

            if (!$currentOrganization) {
                return response()->json(['error' => 'Организация не найдена.'], 404);
            }

            $order = $this->orderService->findOrder($id);
            
            if (!$order) {
                return response()->json(['error' => 'Заказ не найден.'], 404);
            }

            $newStatusId = $request->input('status_id');
            $updatedOrder = $this->orderService->updateOrderStatus($order, $newStatusId, $currentOrganization);

            return response()->json([
                'success' => true,
                'order' => $updatedOrder,
            ]);
        } catch (\Exception $e) {
            \Log::error('Order status update failed: ' . $e->getMessage());
            
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
