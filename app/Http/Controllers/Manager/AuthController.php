<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('manager.auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->authService->login($request->only('email', 'password', 'remember'));

            // Check if manager needs to setup organization
            if ($this->authService->needsOrganizationSetup()) {
                return redirect()->route('manager.organization.select');
            }

            return redirect()->intended(route('manager.dashboard'));
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('manager.auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $manager = $this->authService->register($request->all());
            $this->authService->login($request->only('email', 'password'));

            return redirect()->route('manager.organization.select')
                ->with('success', 'Регистрация успешна! Пожалуйста, создайте или присоединитесь к организации.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Регистрация не удалась. Пожалуйста, попробуйте снова.'])
                ->withInput();
        }
    }

    /**
     * Handle logout request.
     */
    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('manager.login')
            ->with('success', 'Вы успешно вышли из системы.');
    }
}
