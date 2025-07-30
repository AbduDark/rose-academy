<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index() {
    return Course::with('lessons')->get();
}

public function show($id) {
    return Course::with('lessons')->findOrFail($id);
}

public function store(Request $request) {
    $request->validate([
        'title' => 'required|string',
        'description' => 'required',
    ]);
    return Course::create($request->all());
}

public function update(Request $request, $id) {
    $course = Course::findOrFail($id);
    $course->update($request->only('title', 'description'));
    return $course;
}

public function destroy($id) {
    Course::findOrFail($id)->delete();
    return response()->json(['message' => 'Deleted']);
}
}
