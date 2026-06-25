<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::query()->with(['user', 'jobListing']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('content', 'like', "%{$request->search}%");
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function hide(Request $request, Comment $comment)
    {
        Gate::authorize('moderate', $comment);

        $comment->update([
            'status' => 'hidden',
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Comment hidden successfully',
            'data' => $comment->fresh(['user', 'jobListing']),
        ]);
    }

    public function unhide(Request $request, Comment $comment)
    {
        Gate::authorize('moderate', $comment);

        $comment->update([
            'status' => 'visible',
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Comment restored successfully',
            'data' => $comment->fresh(['user', 'jobListing']),
        ]);
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('moderate', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
