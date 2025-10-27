<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    protected OrganizationService $organizationService;
    protected AuthService $authService;

    public function __construct(OrganizationService $organizationService, AuthService $authService)
    {
        $this->organizationService = $organizationService;
        $this->authService = $authService;
    }

    /**
     * Show the organization selection page (create or join).
     */
    public function select()
    {
        $manager = $this->authService->getAuthenticatedManager();

        if (!$manager) {
            return redirect()->route('manager.login');
        }

        // If already has organization, redirect to dashboard
        if ($manager->hasOrganization()) {
            return redirect()->route('manager.dashboard');
        }

        return view('manager.organization.select');
    }

    /**
     * Show the create organization form.
     */
    public function showCreateForm()
    {
        return view('manager.organization.create');
    }

    /**
     * Handle create organization request.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'registration_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'authority_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $manager = $this->authService->getAuthenticatedManager();
            
            $data = $request->except(['registration_document', 'authority_document']);
            
            // Handle file uploads
            if ($request->hasFile('registration_document')) {
                $data['registration_document'] = $request->file('registration_document')->store('organizations/documents', 'public');
            }
            
            if ($request->hasFile('authority_document')) {
                $data['authority_document'] = $request->file('authority_document')->store('organizations/documents', 'public');
            }

            $organization = $this->organizationService->createOrganization($data, $manager);

            return redirect()->route('manager.dashboard')
                ->with('success', 'Организация успешно создана!');
        } catch (\Exception $e) {
            \Log::error('Organization creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Не удалось создать организацию: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the join organization form.
     */
    public function showJoinForm()
    {
        return view('manager.organization.join');
    }

    /**
     * Handle join organization request.
     */
    public function join(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|exists:organizations,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $manager = $this->authService->getAuthenticatedManager();
            $organization = $this->organizationService->joinOrganization($request->organization_id, $manager);

            return redirect()->route('manager.dashboard')
                ->with('success', 'Вы успешно присоединились к ' . $organization->name . '!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Switch to a different organization.
     */
    public function switch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|exists:organizations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid organization'], 400);
        }

        try {
            $manager = $this->authService->getAuthenticatedManager();
            $organization = $this->organizationService->switchOrganization($request->organization_id, $manager);

            return redirect()->back()
                ->with('success', 'Переключено на ' . $organization->name);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
