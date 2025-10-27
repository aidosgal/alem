<?php

namespace App\Services;

use App\Models\Manager;
use App\Repositories\ManagerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected ManagerRepository $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    /**
     * Register a new manager.
     */
    public function register(array $data): Manager
    {
        return $this->managerRepository->create($data);
    }

    /**
     * Authenticate manager and login.
     */
    public function login(array $credentials): bool
    {
        $manager = $this->managerRepository->findByEmail($credentials['email']);

        if (!$manager || !Hash::check($credentials['password'], $manager->user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Указанные учетные данные неверны.'],
            ]);
        }

        Auth::login($manager->user, $credentials['remember'] ?? false);

        return true;
    }

    /**
     * Logout the current manager.
     */
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Get the currently authenticated manager.
     */
    public function getAuthenticatedManager(): ?Manager
    {
        return Auth::user()?->manager;
    }

    /**
     * Check if manager needs to select/create organization.
     */
    public function needsOrganizationSetup(): bool
    {
        $manager = $this->getAuthenticatedManager();
        return $manager && !$manager->hasOrganization();
    }
}
