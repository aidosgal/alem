<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Get list of all organizations
     */
    public function index(Request $request)
    {
        $query = Organization::query();

        // Optional search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $organizations = $query->withCount('vacancies')
            ->select('id', 'name', 'description', 'image', 'email', 'phone')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $organizations->map(function($org) {
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'description' => $org->description,
                    'image' => $org->image,
                    'email' => $org->email,
                    'phone' => $org->phone,
                    'vacancies_count' => $org->vacancies_count ?? 0,
                ];
            })
        ], 200);
    }

    /**
     * Get single organization details
     */
    public function show($id)
    {
        $organization = Organization::with(['services'])->find($id);

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'description' => $organization->description,
                'image' => $organization->image,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'services' => $organization->services->map(function($service) {
                    return [
                        'id' => $service->id,
                        'title' => $service->title,
                        'description' => $service->description,
                        'price' => $service->price,
                        'duration_days' => $service->duration_days,
                        'duration_min_days' => $service->duration_min_days,
                        'duration_max_days' => $service->duration_max_days,
                    ];
                })
            ]
        ], 200);
    }
}

