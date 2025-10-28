<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new applicant
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create user
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create applicant profile
        $applicant = Applicant::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'city' => $request->city,
            'date_of_birth' => $request->date_of_birth,
        ]);

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
                'applicant' => [
                    'id' => $applicant->id,
                    'first_name' => $applicant->first_name,
                    'last_name' => $applicant->last_name,
                    'full_name' => $applicant->full_name,
                    'phone' => $applicant->phone,
                    'city' => $applicant->city,
                    'date_of_birth' => $applicant->date_of_birth,
                    'avatar' => $applicant->avatar,
                ],
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Login applicant
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if user has applicant profile
        $applicant = $user->applicant;
        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant profile not found'
            ], 404);
        }

        // Revoke old tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
                'applicant' => [
                    'id' => $applicant->id,
                    'first_name' => $applicant->first_name,
                    'last_name' => $applicant->last_name,
                    'full_name' => $applicant->full_name,
                    'phone' => $applicant->phone,
                    'city' => $applicant->city,
                    'date_of_birth' => $applicant->date_of_birth,
                    'avatar' => $applicant->avatar,
                ],
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Logout applicant
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
                'applicant' => [
                    'id' => $applicant->id,
                    'first_name' => $applicant->first_name,
                    'last_name' => $applicant->last_name,
                    'full_name' => $applicant->full_name,
                    'phone' => $applicant->phone,
                    'city' => $applicant->city,
                    'date_of_birth' => $applicant->date_of_birth,
                    'avatar' => $applicant->avatar,
                    'balance' => $applicant->balance->balance ?? 0,
                ],
            ]
        ], 200);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
            ]
        ], 200);
    }
}
