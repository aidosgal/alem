<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get applicant profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant profile not found'
            ], 404);
        }

        $documents = $applicant->documents()->get()->map(function($doc) {
            return [
                'id' => $doc->id,
                'type' => $doc->type,
                'file_path' => $doc->file_path,
                'file_url' => Storage::url($doc->file_path),
                'uploaded_at' => $doc->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'applicant' => [
                    'id' => $applicant->id,
                    'first_name' => $applicant->first_name,
                    'last_name' => $applicant->last_name,
                    'full_name' => $applicant->full_name,
                    'email' => $user->email,
                    'phone' => $applicant->phone,
                    'city' => $applicant->city,
                    'date_of_birth' => $applicant->date_of_birth,
                    'avatar' => $applicant->avatar,
                    'balance' => $applicant->balance->balance ?? 0,
                    'documents' => $documents,
                ],
            ]
        ], 200);
    }

    /**
     * Update applicant profile
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'city' => 'sometimes|nullable|string|max:255',
            'date_of_birth' => 'sometimes|nullable|date',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update applicant fields
        if ($request->has('first_name')) {
            $applicant->first_name = $request->first_name;
        }
        if ($request->has('last_name')) {
            $applicant->last_name = $request->last_name;
        }
        if ($request->has('phone')) {
            $applicant->phone = $request->phone;
        }
        if ($request->has('city')) {
            $applicant->city = $request->city;
        }
        if ($request->has('date_of_birth')) {
            $applicant->date_of_birth = $request->date_of_birth;
        }

        $applicant->save();

        // Update user email if provided
        if ($request->has('email')) {
            $user->email = $request->email;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'applicant' => [
                    'id' => $applicant->id,
                    'first_name' => $applicant->first_name,
                    'last_name' => $applicant->last_name,
                    'full_name' => $applicant->full_name,
                    'email' => $user->email,
                    'phone' => $applicant->phone,
                    'city' => $applicant->city,
                    'date_of_birth' => $applicant->date_of_birth,
                    'avatar' => $applicant->avatar,
                ],
            ]
        ], 200);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
            'data' => [
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $applicant = $user->applicant;

        // Delete old avatar
        if ($applicant->avatar) {
            Storage::delete($applicant->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $applicant->avatar = $path;
        $applicant->save();

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar' => $applicant->avatar,
                'avatar_url' => Storage::url($path),
            ]
        ], 200);
    }

    /**
     * Upload document
     */
    public function uploadDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $applicant = $user->applicant;

        // Store document
        $path = $request->file('document')->store('documents', 'public');

        $document = Document::create([
            'applicant_id' => $applicant->id,
            'type' => $request->type,
            'file_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => [
                'document' => [
                    'id' => $document->id,
                    'type' => $document->type,
                    'file_path' => $document->file_path,
                    'file_url' => Storage::url($path),
                    'uploaded_at' => $document->created_at->toISOString(),
                ]
            ]
        ], 201);
    }

    /**
     * Delete document
     */
    public function deleteDocument($id, Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        $document = Document::where('id', $id)
            ->where('applicant_id', $applicant->id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }

        // Delete file
        Storage::delete($document->file_path);

        // Delete record
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ], 200);
    }
}
