<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Vacancy;
use App\Repositories\VacancyRepository;
use Exception;

class VacancyService
{
    protected VacancyRepository $vacancyRepository;

    public function __construct(VacancyRepository $vacancyRepository)
    {
        $this->vacancyRepository = $vacancyRepository;
    }

    /**
     * Get all vacancies for an organization.
     */
    public function getOrganizationVacancies(Organization $organization)
    {
        return $this->vacancyRepository->getByOrganization($organization);
    }

    /**
     * Create a new vacancy for an organization.
     */
    public function createVacancy(array $data, Organization $organization): Vacancy
    {
        $data['organization_id'] = $organization->id;
        
        // Process details if provided
        if (isset($data['salary_from']) || isset($data['salary_to']) || isset($data['location']) || isset($data['employment_type'])) {
            $data['details'] = [
                'salary_from' => $data['salary_from'] ?? null,
                'salary_to' => $data['salary_to'] ?? null,
                'currency' => $data['currency'] ?? 'EUR',
                'location' => $data['location'] ?? null,
                'employment_type' => $data['employment_type'] ?? null,
            ];
            
            unset($data['salary_from'], $data['salary_to'], $data['currency'], $data['location'], $data['employment_type']);
        }

        return $this->vacancyRepository->create($data);
    }

    /**
     * Update a vacancy.
     */
    public function updateVacancy(Vacancy $vacancy, array $data, Organization $organization): Vacancy
    {
        if (!$this->vacancyRepository->belongsToOrganization($vacancy, $organization)) {
            throw new Exception('У вас нет прав для редактирования этой вакансии.');
        }

        // Process details if provided
        if (isset($data['salary_from']) || isset($data['salary_to']) || isset($data['location']) || isset($data['employment_type'])) {
            $data['details'] = [
                'salary_from' => $data['salary_from'] ?? null,
                'salary_to' => $data['salary_to'] ?? null,
                'currency' => $data['currency'] ?? 'EUR',
                'location' => $data['location'] ?? null,
                'employment_type' => $data['employment_type'] ?? null,
            ];
            
            unset($data['salary_from'], $data['salary_to'], $data['currency'], $data['location'], $data['employment_type']);
        }

        return $this->vacancyRepository->update($vacancy, $data);
    }

    /**
     * Delete a vacancy.
     */
    public function deleteVacancy(Vacancy $vacancy, Organization $organization): bool
    {
        if (!$this->vacancyRepository->belongsToOrganization($vacancy, $organization)) {
            throw new Exception('У вас нет прав для удаления этой вакансии.');
        }

        return $this->vacancyRepository->delete($vacancy);
    }

    /**
     * Find vacancy by ID.
     */
    public function findVacancy(string $id): ?Vacancy
    {
        return $this->vacancyRepository->findById($id);
    }
}
