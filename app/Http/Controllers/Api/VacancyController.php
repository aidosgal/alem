<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    /**
     * Get list of vacancies with filters
     */
    public function index(Request $request)
    {
        $query = Vacancy::with(['organization']);

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

        // Filter by location (stored in details->location)
        if ($request->has('location')) {
            $query->whereRaw("details->>'location' ILIKE ?", ["%{$request->location}%"]);
        }

        // Filter by employment type (stored in details->employment_type)
        if ($request->has('employment_type')) {
            $query->whereRaw("details->>'employment_type' = ?", [$request->employment_type]);
        }

        // Filter by salary range
        if ($request->has('min_salary')) {
            $query->whereRaw("CAST(details->>'salary_from' AS DECIMAL) >= ?", [$request->min_salary]);
        }

        if ($request->has('max_salary')) {
            $query->whereRaw("CAST(details->>'salary_to' AS DECIMAL) <= ?", [$request->max_salary]);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['created_at', 'updated_at', 'title'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $vacancies = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'vacancies' => $vacancies->map(function($vacancy) {
                    return [
                        'id' => $vacancy->id,
                        'title' => $vacancy->title,
                        'description' => $vacancy->description,
                        'details' => $vacancy->details,
                        'organization' => [
                            'id' => $vacancy->organization->id,
                            'name' => $vacancy->organization->name,
                            'image' => $vacancy->organization->image,
                        ],
                        'created_at' => $vacancy->created_at->toISOString(),
                        'updated_at' => $vacancy->updated_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'total' => $vacancies->total(),
                    'per_page' => $vacancies->perPage(),
                    'current_page' => $vacancies->currentPage(),
                    'last_page' => $vacancies->lastPage(),
                    'from' => $vacancies->firstItem(),
                    'to' => $vacancies->lastItem(),
                ]
            ]
        ], 200);
    }

    /**
     * Get single vacancy
     */
    public function show($id)
    {
        $vacancy = Vacancy::with(['organization'])->find($id);

        if (!$vacancy) {
            return response()->json([
                'success' => false,
                'message' => 'Vacancy not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'vacancy' => [
                    'id' => $vacancy->id,
                    'title' => $vacancy->title,
                    'description' => $vacancy->description,
                    'details' => $vacancy->details,
                    'organization' => [
                        'id' => $vacancy->organization->id,
                        'name' => $vacancy->organization->name,
                        'description' => $vacancy->organization->description,
                        'image' => $vacancy->organization->image,
                        'email' => $vacancy->organization->email,
                        'phone' => $vacancy->organization->phone,
                    ],
                    'created_at' => $vacancy->created_at->toISOString(),
                    'updated_at' => $vacancy->updated_at->toISOString(),
                ]
            ]
        ], 200);
    }

    /**
     * Get available cities for filter
     */
    public function cities()
    {
        // Since location is stored in details jsonb field, we need to extract unique values
        $vacancies = Vacancy::whereNotNull('details')->get();
        
        $locations = $vacancies->pluck('details.location')
            ->filter()
            ->unique()
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'locations' => $locations
            ]
        ], 200);
    }

    /**
     * Get vacancy employment types for filter
     */
    public function types()
    {
        // Since employment_type is stored in details jsonb field, we need to extract unique values
        $vacancies = Vacancy::whereNotNull('details')->get();
        
        $types = $vacancies->pluck('details.employment_type')
            ->filter()
            ->unique()
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'employment_types' => $types
            ]
        ], 200);
    }
}
