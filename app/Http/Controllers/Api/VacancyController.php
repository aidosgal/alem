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
        $query = Vacancy::with(['organization'])
            ->where('status', 'active');

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by salary range
        if ($request->has('min_salary')) {
            $query->where('salary_from', '>=', $request->min_salary);
        }

        if ($request->has('max_salary')) {
            $query->where('salary_to', '<=', $request->max_salary);
        }

        // Filter by organization
        if ($request->has('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

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
                        'requirements' => $vacancy->requirements,
                        'type' => $vacancy->type,
                        'city' => $vacancy->city,
                        'address' => $vacancy->address,
                        'salary_from' => $vacancy->salary_from,
                        'salary_to' => $vacancy->salary_to,
                        'salary_display' => $vacancy->salary_display,
                        'status' => $vacancy->status,
                        'organization' => [
                            'id' => $vacancy->organization->id,
                            'name' => $vacancy->organization->name,
                            'logo' => $vacancy->organization->logo,
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
                    'requirements' => $vacancy->requirements,
                    'type' => $vacancy->type,
                    'city' => $vacancy->city,
                    'address' => $vacancy->address,
                    'salary_from' => $vacancy->salary_from,
                    'salary_to' => $vacancy->salary_to,
                    'salary_display' => $vacancy->salary_display,
                    'status' => $vacancy->status,
                    'organization' => [
                        'id' => $vacancy->organization->id,
                        'name' => $vacancy->organization->name,
                        'description' => $vacancy->organization->description,
                        'logo' => $vacancy->organization->logo,
                        'address' => $vacancy->organization->address,
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
        $cities = Vacancy::where('status', 'active')
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'cities' => $cities
            ]
        ], 200);
    }

    /**
     * Get vacancy types for filter
     */
    public function types()
    {
        $types = Vacancy::where('status', 'active')
            ->whereNotNull('type')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'types' => $types
            ]
        ], 200);
    }
}
