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
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%")
                  ->orWhere('city', 'ILIKE', "%{$search}%");
            });
        }

        // Optional city filter
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        $organizations = $query->select('id', 'name', 'description', 'city', 'address')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $organizations
        ], 200);
    }

    /**
     * Get single organization details
     */
    public function show($id)
    {
        $organization = Organization::find($id);

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $organization
        ], 200);
    }
}

