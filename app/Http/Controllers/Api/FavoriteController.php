<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function add(Request $request, $course_id)
    {
        $request->user()->favorites()->firstOrCreate([
            'course_id' => $course_id
        ]);

        return response()->json(['message' => 'Added to favorites']);
    }

    public function remove(Request $request, $course_id)
    {
        $request->user()->favorites()->where('course_id', $course_id)->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }
}
