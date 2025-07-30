<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;

class LessonController extends Controller
{
public function index($course_id) {
    return Lesson::where('course_id', $course_id)->get();
}

public function store(Request $request) {
    $request->validate([
        'title' => 'required|string',
        'course_id' => 'required|exists:courses,id',
        'video_url' => 'required|url',
    ]);
    return Lesson::create($request->all());
}

public function update(Request $request, $id) {
    $lesson = Lesson::findOrFail($id);
    $lesson->update($request->only('title', 'video_url'));
    return $lesson;
}

public function destroy($id) {
    Lesson::findOrFail($id)->delete();
    return response()->json(['message' => 'Lesson deleted']);
}
}
