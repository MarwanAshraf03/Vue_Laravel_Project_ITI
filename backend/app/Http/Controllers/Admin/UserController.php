<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function show(User $user)
    {
        return response()->json($user->load('profile'));
    }

    public function ban(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot ban your own account'], 422);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Admin accounts cannot be banned'], 422);
        }

        $user->update(['status' => 'banned']);
        $user->tokens()->delete();

        return response()->json([
            'message' => 'User banned successfully',
            'data' => $user->fresh(),
        ]);
    }

    public function unban(Request $request, User $user)
    {
        $user->update(['status' => 'active']);

        return response()->json([
            'message' => 'User unbanned successfully',
            'data' => $user->fresh(),
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete your own account'], 422);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return response()->json(['message' => 'Cannot delete the only remaining admin'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
