<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($lessonId)
    {
        $comments = Comment::with('user:id,name')
            ->where('lesson_id', $lessonId)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'lesson_id' => $request->lesson_id,
            'content' => $request->content,
            'is_approved' => false, // Requires admin approval
        ]);

        return response()->json([
            'message' => 'Comment submitted successfully. It will be visible after approval.',
            'comment' => $comment
        ], 201);
    }
}
