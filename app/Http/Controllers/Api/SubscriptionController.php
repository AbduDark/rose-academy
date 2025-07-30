<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Course;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function mySubscriptions(Request $request)
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with('course')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->get();

        return response()->json($subscriptions);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = $request->user();
        $courseId = $request->course_id;

        // Check if already subscribed
        if ($user->isSubscribedTo($courseId)) {
            return response()->json(['message' => 'Already subscribed to this course'], 400);
        }

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'subscribed_at' => now(),
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Successfully subscribed to course',
            'subscription' => $subscription
        ], 201);
    }

    public function unsubscribe(Request $request, $courseId)
    {
        $user = $request->user();

        $subscription = Subscription::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('is_active', true)
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $subscription->update(['is_active' => false]);

        return response()->json(['message' => 'Successfully unsubscribed from course']);
    }
}
