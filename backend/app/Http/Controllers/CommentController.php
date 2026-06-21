<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Public: list visible comments on an approved job listing.
     */
    public function index(JobListing $jobListing)
    {
        $comments = $jobListing->comments()
            ->where('status', 'visible')
            ->with('user:id,name,profile_picture')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    /**
     * Authenticated: post a comment on an approved, published job listing.
     */
    public function store(Request $request, JobListing $jobListing)
    {
        if (! $jobListing->isPubliclyVisible()) {
            return response()->json(['message' => 'Comments are only allowed on approved jobs'], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'job_listing_id' => $jobListing->id,
            'content' => $validator->validated()['content'],
        ]);

        return response()->json([
            'message' => 'Comment posted successfully',
            'data' => $comment->load('user:id,name,profile_picture'),
        ], 201);
    }

    /**
     * Authenticated: delete your own comment.
     */
    public function destroy(Request $request, Comment $comment)
    {
        abort_unless(
            $comment->user_id === $request->user()->id || $request->user()->role === 'admin',
            403,
            'Unauthorized action'
        );

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
