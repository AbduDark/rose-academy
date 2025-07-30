<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index($courseId, Request $request)
    {
        $course = Course::findOrFail($courseId);

        // Check if user is subscribed or if lesson is free
        $user = $request->user();
        $isSubscribed = $user ? $user->isSubscribedTo($courseId) : false;

        $lessons = $course->lessons();

        if (!$isSubscribed) {
            $lessons = $lessons->where('is_free', true);
        }

        return response()->json($lessons->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free' => 'boolean',
        ]);

        $lesson = Lesson::create($request->all());

        return response()->json($lesson, 201);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'content' => 'sometimes|string',
            'video_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free' => 'boolean',
        ]);

        $lesson->update($request->all());

        return response()->json($lesson);
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return response()->json(['message' => 'Lesson deleted successfully']);
    }
}
