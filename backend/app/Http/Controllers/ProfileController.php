<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function candidateSignUp(Request $request){
        $profile_picture = null;
        if($request->hasFile('profile_picture')){
            $profile_picture = $request->profile_picture->store('profile_pictures');
        }   
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'candidate',
            'profile_picture' => $profile_picture,
        ])->profile()->create([
            'job_title' => $request->job_title,
        ]);
        return response()->json([
            'message' => 'Profile created successfully',
            'user' => $user
        ], 201);
    }
    public function adminSignUp(Request $request){
        $profile_picture = null;
        if($request->hasFile('profile_picture')){
            $profile_picture = $request->profile_picture->store('profile_pictures');
        }   
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin',
            'profile_picture' => $profile_picture,
        ])->profile()->create([
        ]);
        return response()->json([
            'message' => 'Profile created successfully',
            'user' => $user
        ], 201);
    }
    public function employerSignUp(Request $request){
        $profile_picture = null;
        if($request->hasFile('profile_picture')){
            $profile_picture = $request->profile_picture->store('profile_pictures');
        }   
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'employer',
            'profile_picture' => $profile_picture,
        ])->profile()->create([
            'phone' => $request->phone,
            'description' => $request->description,
        ]);
        return response()->json([
            'message' => 'Profile created successfully',
            'user' => $user
        ], 201);
    }
}
