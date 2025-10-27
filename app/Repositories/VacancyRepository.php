<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Collection;

class VacancyRepository
{
    /**
     * Get all vacancies for an organization.
     */
    public function getByOrganization(Organization $organization): Collection
    {
        return Vacancy::where('organization_id', $organization->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find vacancy by ID.
     */
    public function findById(string $id): ?Vacancy
    {
        return Vacancy::find($id);
    }

    /**
     * Create a new vacancy.
     */
    public function create(array $data): Vacancy
    {
        return Vacancy::create($data);
    }

    /**
     * Update a vacancy.
     */
    public function update(Vacancy $vacancy, array $data): Vacancy
    {
        $vacancy->update($data);
        return $vacancy->fresh();
    }

    /**
     * Delete a vacancy.
     */
    public function delete(Vacancy $vacancy): bool
    {
        return $vacancy->delete();
    }

    /**
     * Check if vacancy belongs to organization.
     */
    public function belongsToOrganization(Vacancy $vacancy, Organization $organization): bool
    {
        return $vacancy->organization_id === $organization->id;
    }
}
