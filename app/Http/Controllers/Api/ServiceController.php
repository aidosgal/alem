<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get list of services with filters
     */
    public function index(Request $request)
    {
        $query = Service::with(['organization']);

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by organization
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Allow sorting by popular (orders count) or standard fields
        if ($sortBy === 'popular') {
            $query->withCount('orders')->orderBy('orders_count', 'desc');
        } elseif (in_array($sortBy, ['created_at', 'updated_at', 'title', 'price'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $services = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services->map(function($service) {
                    return [
                        'id' => $service->id,
                        'title' => $service->title,
                        'description' => $service->description,
                        'price' => $service->price,
                        'duration_days' => $service->duration_days,
                        'duration_min_days' => $service->duration_min_days,
                        'duration_max_days' => $service->duration_max_days,
                        'organization' => [
                            'id' => $service->organization->id,
                            'name' => $service->organization->name,
                            'image' => $service->organization->image,
                        ],
                        'created_at' => $service->created_at->toISOString(),
                        'updated_at' => $service->updated_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'total' => $services->total(),
                    'per_page' => $services->perPage(),
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'from' => $services->firstItem(),
                    'to' => $services->lastItem(),
                ]
            ]
        ], 200);
    }

    /**
     * Get single service
     */
    public function show($id)
    {
        $service = Service::with(['organization'])->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'service' => [
                    'id' => $service->id,
                    'title' => $service->title,
                    'description' => $service->description,
                    'price' => $service->price,
                    'duration_days' => $service->duration_days,
                    'duration_min_days' => $service->duration_min_days,
                    'duration_max_days' => $service->duration_max_days,
                    'organization' => [
                        'id' => $service->organization->id,
                        'name' => $service->organization->name,
                        'description' => $service->organization->description,
                        'image' => $service->organization->image,
                        'email' => $service->organization->email,
                        'phone' => $service->organization->phone,
                    ],
                    'created_at' => $service->created_at->toISOString(),
                    'updated_at' => $service->updated_at->toISOString(),
                ]
            ]
        ], 200);
    }

    /**
     * Get available categories for filter
     * Note: Categories would need to be stored in a details JSONB field
     * This endpoint is kept for future implementation
     */
    public function categories()
    {
        // Since there's no category field in the database,
        // return empty array for now
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => []
            ]
        ], 200);
    }
}
