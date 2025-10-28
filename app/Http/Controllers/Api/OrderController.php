<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get list of orders for applicant
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant profile not found'
            ], 404);
        }

        $query = Order::where('applicant_id', $applicant->id)
            ->with(['services', 'status', 'organization']);

        // Filter by status
        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by organization
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'price' => $order->price,
                        'status' => $order->status ? [
                            'id' => $order->status->id,
                            'name' => $order->status->name,
                            'color' => $order->status->color,
                        ] : null,
                        'organization' => [
                            'id' => $order->organization->id,
                            'name' => $order->organization->name,
                            'logo' => $order->organization->logo,
                        ],
                        'services_count' => $order->services->count(),
                        'created_at' => $order->created_at->toISOString(),
                        'updated_at' => $order->updated_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ]
            ]
        ], 200);
    }

    /**
     * Get single order
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        $order = Order::where('id', $id)
            ->where('applicant_id', $applicant->id)
            ->with(['services', 'status', 'organization'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order' => [
                    'id' => $order->id,
                    'price' => $order->price,
                    'status' => $order->status ? [
                        'id' => $order->status->id,
                        'name' => $order->status->name,
                        'color' => $order->status->color,
                        'description' => $order->status->description,
                    ] : null,
                    'organization' => [
                        'id' => $order->organization->id,
                        'name' => $order->organization->name,
                        'logo' => $order->organization->logo,
                        'address' => $order->organization->address,
                        'phone' => $order->organization->phone,
                    ],
                    'services' => $order->services->map(function($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'description' => $service->description,
                            'price' => $service->pivot->price,
                            'quantity' => $service->pivot->quantity ?? 1,
                        ];
                    }),
                    'created_at' => $order->created_at->toISOString(),
                    'updated_at' => $order->updated_at->toISOString(),
                ]
            ]
        ], 200);
    }

    /**
     * Get order statuses
     */
    public function statuses(Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        // Get unique statuses from applicant's orders
        $statuses = Order::where('applicant_id', $applicant->id)
            ->with('status')
            ->get()
            ->pluck('status')
            ->filter()
            ->unique('id')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'statuses' => $statuses->map(function($status) {
                    return [
                        'id' => $status->id,
                        'name' => $status->name,
                        'color' => $status->color,
                    ];
                })
            ]
        ], 200);
    }
}
