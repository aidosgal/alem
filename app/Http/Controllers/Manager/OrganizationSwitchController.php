<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationSwitchController extends Controller
{
    /**
     * Show all organizations for manager.
     */
    public function index(Request $request)
    {
        $manager = $request->user()->manager;
        
        // Get organizations where manager is owner
        $ownedOrganizations = $manager->ownedOrganizations()->get();
        
        // Get organizations where manager is member
        $memberOrganizations = $manager->memberOrganizations()->get();
        
        $currentOrganization = $manager->currentOrganization();

        return view('manager.organizations.index', compact(
            'ownedOrganizations',
            'memberOrganizations',
            'currentOrganization'
        ));
    }

    /**
     * Switch to a different organization.
     */
    public function switch(Request $request, string $organizationId)
    {
        $manager = $request->user()->manager;
        $organization = Organization::findOrFail($organizationId);

        // Check if manager has access to this organization (owner or member)
        $hasAccess = $manager->organizations()->where('organizations.id', $organization->id)->exists();

        if (!$hasAccess) {
            return redirect()->route('manager.organizations.index')
                ->with('error', 'У вас нет доступа к этой организации.');
        }

        // Store selected organization in session
        session(['current_organization_id' => $organization->id]);

        return redirect()->route('manager.dashboard')
            ->with('success', "Переключено на организацию: {$organization->name}");
    }

    /**
     * Show form to create new organization.
     */
    public function create()
    {
        return view('manager.organizations.create');
    }

    /**
     * Store new organization.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
        ]);

        $manager = $request->user()->manager;

        DB::beginTransaction();
        try {
            // Create organization
            $organization = Organization::create([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address ?? '',
                'phone' => $request->phone ?? '',
                'email' => $request->user()->email,
                'registration_document' => '',
                'authority_document' => '',
            ]);

            // Attach manager as owner
            $manager->organizations()->attach($organization->id, ['role' => 'owner']);

            // Switch to new organization
            session(['current_organization_id' => $organization->id]);

            DB::commit();

            return redirect()->route('manager.dashboard')
                ->with('success', "Организация '{$organization->name}' успешно создана!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при создании организации: ' . $e->getMessage());
        }
    }

    /**
     * Show form to join organization.
     */
    public function joinForm()
    {
        return view('manager.organizations.join');
    }

    /**
     * Join existing organization by code.
     */
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        // Find organization by code (using ID as code)
        $organization = Organization::where('id', $request->code)->first();

        if (!$organization) {
            return back()->withErrors(['code' => 'Организация с таким кодом не найдена.']);
        }

        $manager = $request->user()->manager;

        // Check if already a member or owner
        $existingRelation = $manager->organizations()
            ->where('organizations.id', $organization->id)
            ->exists();

        if ($existingRelation) {
            return back()->withErrors(['code' => 'Вы уже являетесь участником этой организации.']);
        }

        // Add manager to organization as member
        $manager->organizations()->attach($organization->id, ['role' => 'member']);

        // Switch to this organization
        session(['current_organization_id' => $organization->id]);

        return redirect()->route('manager.dashboard')
            ->with('success', "Вы успешно присоединились к организации '{$organization->name}'!");
    }
}
