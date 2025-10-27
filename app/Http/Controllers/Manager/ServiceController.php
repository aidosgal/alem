<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\OrganizationService;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    protected AuthService $authService;
    protected OrganizationService $organizationService;
    protected ServiceService $serviceService;

    public function __construct(
        AuthService $authService,
        OrganizationService $organizationService,
        ServiceService $serviceService
    ) {
        $this->authService = $authService;
        $this->organizationService = $organizationService;
        $this->serviceService = $serviceService;
    }

    /**
     * Display a listing of services.
     */
    public function index()
    {
        $manager = $this->authService->getAuthenticatedManager();
        $currentOrganization = $this->organizationService->getCurrentOrganization($manager);
        
        if (!$currentOrganization) {
            return redirect()->route('manager.organization.select')
                ->with('error', 'Пожалуйста, выберите организацию.');
        }

        $services = $this->serviceService->getOrganizationServices($currentOrganization);

        return view('manager.services.index', compact('services', 'currentOrganization'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('manager.services.create');
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
            'duration_min_days' => 'nullable|integer|min:0',
            'duration_max_days' => 'nullable|integer|min:0',
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

            $this->serviceService->createService($request->all(), $currentOrganization);

            return redirect()->route('manager.services.index')
                ->with('success', 'Услуга успешно создана!');
        } catch (\Exception $e) {
            \Log::error('Service creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Не удалось создать услугу: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(string $id)
    {
        $service = $this->serviceService->findService($id);

        if (!$service) {
            return redirect()->route('manager.services.index')
                ->with('error', 'Услуга не найдена.');
        }

        $manager = $this->authService->getAuthenticatedManager();
        $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

        if ($service->organization_id !== $currentOrganization->id) {
            return redirect()->route('manager.services.index')
                ->with('error', 'У вас нет прав для редактирования этой услуги.');
        }

        return view('manager.services.edit', compact('service'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
            'duration_min_days' => 'nullable|integer|min:0',
            'duration_max_days' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $service = $this->serviceService->findService($id);

            if (!$service) {
                return redirect()->route('manager.services.index')
                    ->with('error', 'Услуга не найдена.');
            }

            $manager = $this->authService->getAuthenticatedManager();
            $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

            $this->serviceService->updateService($service, $request->all(), $currentOrganization);

            return redirect()->route('manager.services.index')
                ->with('success', 'Услуга успешно обновлена!');
        } catch (\Exception $e) {
            \Log::error('Service update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Не удалось обновить услугу: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified service.
     */
    public function destroy(string $id)
    {
        try {
            $service = $this->serviceService->findService($id);

            if (!$service) {
                return redirect()->route('manager.services.index')
                    ->with('error', 'Услуга не найдена.');
            }

            $manager = $this->authService->getAuthenticatedManager();
            $currentOrganization = $this->organizationService->getCurrentOrganization($manager);

            $this->serviceService->deleteService($service, $currentOrganization);

            return redirect()->route('manager.services.index')
                ->with('success', 'Услуга успешно удалена!');
        } catch (\Exception $e) {
            \Log::error('Service deletion failed: ' . $e->getMessage());
            
            return redirect()->route('manager.services.index')
                ->with('error', 'Не удалось удалить услугу: ' . $e->getMessage());
        }
    }
}
