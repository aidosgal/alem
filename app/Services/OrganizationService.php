<?php

namespace App\Services;

use App\Models\Manager;
use App\Models\Organization;
use App\Repositories\OrganizationRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class OrganizationService
{
    protected OrganizationRepository $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * Create a new organization and attach manager as owner.
     */
    public function createOrganization(array $data, Manager $manager): Organization
    {
        return DB::transaction(function () use ($data, $manager) {
            $organization = $this->organizationRepository->create($data);
            $this->organizationRepository->attachManager($organization, $manager);
            
            // Set as current organization
            session(['current_organization_id' => $organization->id]);
            
            return $organization;
        });
    }

    /**
     * Join an existing organization.
     */
    public function joinOrganization(string $organizationId, Manager $manager): Organization
    {
        $organization = $this->organizationRepository->findById($organizationId);

        if (!$organization) {
            throw new Exception('Организация не найдена.');
        }

        $this->organizationRepository->attachManager($organization, $manager);
        
        // Set as current organization
        session(['current_organization_id' => $organization->id]);

        return $organization;
    }

    /**
     * Switch to a different organization.
     */
    public function switchOrganization(string $organizationId, Manager $manager): Organization
    {
        $organization = $this->organizationRepository->findById($organizationId);

        if (!$organization) {
            throw new Exception('Организация не найдена.');
        }

        if (!$this->organizationRepository->managerBelongsToOrganization($organization, $manager)) {
            throw new Exception('Вы не принадлежите к этой организации.');
        }

        session(['current_organization_id' => $organization->id]);

        return $organization;
    }

    /**
     * Get all organizations for a manager.
     */
    public function getManagerOrganizations(Manager $manager)
    {
        return $this->organizationRepository->getManagerOrganizations($manager);
    }

    /**
     * Get current active organization.
     */
    public function getCurrentOrganization(Manager $manager): ?Organization
    {
        $currentOrgId = session('current_organization_id');
        
        if ($currentOrgId) {
            $org = $this->organizationRepository->findById($currentOrgId);
            if ($org && $this->organizationRepository->managerBelongsToOrganization($org, $manager)) {
                return $org;
            }
        }

        // Return first organization if no current set
        $organizations = $this->getManagerOrganizations($manager);
        $firstOrg = $organizations->first();
        
        if ($firstOrg) {
            session(['current_organization_id' => $firstOrg->id]);
        }
        
        return $firstOrg;
    }
}
