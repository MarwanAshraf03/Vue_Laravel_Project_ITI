<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    private function mergedUserPayload(User $user): array
    {
        $profile = match ($user->role) {
            'candidate' => Candidate::where('user_id', $user->id)->first(),
            default => $user->loadMissing('profile')->profile,
        };

        return array_merge($user->toArray(), $profile?->toArray() ?? []);
    }

    public function candidateSignUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'job_title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Profile creation failed'
            ], 422);
        }
        $validated = $validator->validated();
        $profile_picture = null;
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->profile_picture->store('profile_pictures');
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'candidate',
            'profile_picture' => $profile_picture,
        ])->profile()->create(['job_title' => $validated['job_title'],]);
        return response()->json(['message' => 'Profile created successfully', 'user' => $user], 201);
    }
    public function adminSignUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Profile creation failed'
            ], 422);
        }
        $validated = $validator->validated();
        $profile_picture = null;
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->profile_picture->store('profile_pictures');
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
            'profile_picture' => $profile_picture,
        ])->profile()->create([]);
        return response()->json([
            'message' => 'Profile created successfully',
            'user' => $user
        ], 201);
    }
    public function employerSignUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:15',
            'description' => 'required|string|max:255',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Profile creation failed'
            ], 422);
        }
        $validated = $validator->validated();
        $profile_picture = null;
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->profile_picture->store('profile_pictures');
        }
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'employer',
            'profile_picture' => $profile_picture,
        ])->profile()->create([
            'phone' => $validated['phone'],
            'description' => $validated['description'],
        ]);
        return response()->json([
            'message' => 'Profile created successfully',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Login failed'
            ], 422);
        }
        $validated = $validator->validated();

        if (! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function get_current_user(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $this->mergedUserPayload($user)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function updateCandidateProfile(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'candidate') {
            return response()->json([
                'message' => 'Unauthorized action'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'job_title' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Profile update failed'
            ], 422);
        }

        $validated = $validator->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user->save();
        Candidate::updateOrCreate(
            ['user_id' => $user->id],
            ['job_title' => $validated['job_title']]
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $this->mergedUserPayload($user->fresh())
        ]);
    }

    public function deleteCandidateProfile(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'candidate') {
            return response()->json([
                'message' => 'Unauthorized action'
            ], 403);
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json([
            'message' => 'Profile deleted successfully'
        ]);
    }
}
