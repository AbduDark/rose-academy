<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'content' => 'required|string'
        ]);

        return Comment::create([
            'user_id' => Auth::id(), // ✅ مضمون
            'lesson_id' => $request->lesson_id,
            'content' => $request->input('content'),

        ]);
    }

    public function index($lesson_id)
    {
        return Comment::where('lesson_id', $lesson_id)->with('user')->get();
    }
}
