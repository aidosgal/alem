<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the manager profile.
     */
    public function show(Request $request)
    {
        $manager = $request->user()->manager;
        $user = $request->user();

        return view('manager.profile.show', compact('manager', 'user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit(Request $request)
    {
        $manager = $request->user()->manager;
        $user = $request->user();

        return view('manager.profile.edit', compact('manager', 'user'));
    }

    /**
     * Update the manager profile.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = $request->user();
        $manager = $user->manager;

        // Update user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Update manager
        $manager->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);

        return redirect()->route('manager.profile.show')
            ->with('success', 'Профиль успешно обновлен.');
    }
}
