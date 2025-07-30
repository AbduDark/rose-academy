<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function mySubscriptions(Request $request)
    {
        return response()->json(
            $request->user()->subscriptions()->with('course')->get()
        );
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $user = $request->user();

        if ($user->subscriptions()->where('course_id', $request->course_id)->exists()) {
            return response()->json(['message' => 'Already subscribed'], 409);
        }

        $user->subscriptions()->create(['course_id' => $request->course_id]);

        return response()->json(['message' => 'Subscribed successfully']);
    }

    public function unsubscribe(Request $request, $course_id)
    {
        $request->user()->subscriptions()->where('course_id', $course_id)->delete();

        return response()->json(['message' => 'Unsubscribed successfully']);
    }
}
