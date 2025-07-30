<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        return Rating::create([
            'user_id' => $request->user()->id,
            'course_id' => $request->course_id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);
    }

    public function index($course_id)
    {
        return Rating::where('course_id', $course_id)->with('user')->get();
    }
}
