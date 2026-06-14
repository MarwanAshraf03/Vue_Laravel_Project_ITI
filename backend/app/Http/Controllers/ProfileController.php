<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function candidateSignUp(Request $request){
    //    $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:6|confirmed',
    //         'job_title' => 'required|string|max:255',
    //     ]);
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
}
