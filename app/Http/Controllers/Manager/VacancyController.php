<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\OrganizationService;
use App\Services\VacancyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VacancyController extends Controller
{
    protected AuthService $authService;
    protected OrganizationService $organizationService;
    protected VacancyService $vacancyService;

    public function __construct(
        AuthService $authService,
        OrganizationService $organizationService,
        VacancyService $vacancyService
    ) {
        $this->authService = $authService;
        $this->organizationService = $organizationService;
        $this->vacancyService = $vacancyService;
    }

    /**
     * Display a listing of vacancies.
     */
    public function index()
    {
        $manager = $this->authService->getAuthenticatedManager();
        $currentOrganization = $this->organizationService->getCurrentOrganization($manager);
        
        if (!$currentOrganization) {
            return redirect()->route('manager.organization.select')
                ->with('error', 'Пожалуйста, выберите организацию.');
        }

        $vacancies = $this->vacancyService->getOrganizationVacancies($currentOrganization);

        return view('manager.vacancies.index', compact('vacancies', 'currentOrganization'));
    }

    /**
     * Show the form for creating a new vacancy.
     */
    public function create()
    {
        return view('manager.vacancies.create');
    }

    /**
     * Store a newly created vacancy.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'salary_from' => 'nullable|numeric|min:0',
            'salary_to' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $manager = $this->authService->getAuthenticatedManager();
            $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

            if (!$currentOrganization) {
                return redirect()->route('manager.organization.select')
                    ->with('error', 'Пожалуйста, выберите организацию.');
            }

            $this->vacancyService->createVacancy($request->all(), $currentOrganization);

            return redirect()->route('manager.vacancies.index')
                ->with('success', 'Вакансия успешно создана!');
        } catch (\Exception $e) {
            \Log::error('Vacancy creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Не удалось создать вакансию: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified vacancy.
     */
    public function edit(string $id)
    {
        $vacancy = $this->vacancyService->findVacancy($id);

        if (!$vacancy) {
            return redirect()->route('manager.vacancies.index')
                ->with('error', 'Вакансия не найдена.');
        }

        $manager = $this->authService->getAuthenticatedManager();
        $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

        if ($vacancy->organization_id !== $currentOrganization->id) {
            return redirect()->route('manager.vacancies.index')
                ->with('error', 'У вас нет прав для редактирования этой вакансии.');
        }

        return view('manager.vacancies.edit', compact('vacancy'));
    }

    /**
     * Update the specified vacancy.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'salary_from' => 'nullable|numeric|min:0',
            'salary_to' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $vacancy = $this->vacancyService->findVacancy($id);

            if (!$vacancy) {
                return redirect()->route('manager.vacancies.index')
                    ->with('error', 'Вакансия не найдена.');
            }

            $manager = $this->authService->getAuthenticatedManager();
            $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

            $this->vacancyService->updateVacancy($vacancy, $request->all(), $currentOrganization);

            return redirect()->route('manager.vacancies.index')
                ->with('success', 'Вакансия успешно обновлена!');
        } catch (\Exception $e) {
            \Log::error('Vacancy update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Не удалось обновить вакансию: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified vacancy.
     */
    public function destroy(string $id)
    {
        try {
            $vacancy = $this->vacancyService->findVacancy($id);

            if (!$vacancy) {
                return redirect()->route('manager.vacancies.index')
                    ->with('error', 'Вакансия не найдена.');
            }

            $manager = $this->authService->getAuthenticatedManager();
            $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

            $this->vacancyService->deleteVacancy($vacancy, $currentOrganization);

            return redirect()->route('manager.vacancies.index')
                ->with('success', 'Вакансия успешно удалена!');
        } catch (\Exception $e) {
            \Log::error('Vacancy deletion failed: ' . $e->getMessage());
            
            return redirect()->route('manager.vacancies.index')
                ->with('error', 'Не удалось удалить вакансию: ' . $e->getMessage());
        }
    }
}
