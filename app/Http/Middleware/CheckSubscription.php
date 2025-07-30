<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Carbon\Carbon;


class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $courseId = $request->route('course_id') ?? $request->course_id;

        $subscription = Subscription::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->whereDate('end_date', '>=', Carbon::now())
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'You are not subscribed to this course.'], 403);
        }

        return $next($request);
    }
}
