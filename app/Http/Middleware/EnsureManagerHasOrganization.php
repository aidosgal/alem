<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagerHasOrganization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $manager = Auth::user()?->manager;

        if (!$manager) {
            return redirect()->route('manager.login');
        }

        // Check if manager has at least one organization
        if (!$manager->hasOrganization()) {
            return redirect()->route('manager.organization.select')
                ->with('error', 'Пожалуйста, создайте или присоединитесь к организации для продолжения.');
        }

        return $next($request);
    }
}
