<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\OrganizationService;

class DashboardController extends Controller
{
    protected AuthService $authService;
    protected OrganizationService $organizationService;

    public function __construct(AuthService $authService, OrganizationService $organizationService)
    {
        $this->authService = $authService;
        $this->organizationService = $organizationService;
    }

    /**
     * Show the dashboard.
     */
    public function index()
    {
        $manager = $this->authService->getAuthenticatedManager();
        $currentOrganization = $this->organizationService->getCurrentOrganization($manager);
        $organizations = $this->organizationService->getManagerOrganizations($manager);

        return view('manager.dashboard', compact('manager', 'currentOrganization', 'organizations'));
    }
}
