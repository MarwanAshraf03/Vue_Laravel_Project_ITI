<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
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
            'user' => array_merge($user->toArray(), $user->profile->toArray())
        ]);
    }
}
