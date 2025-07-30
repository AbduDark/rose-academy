<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GenderContentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->gender === 'male') {
            $request->merge(['video_gender' => 'video_url_male']);
        } else {
            $request->merge(['video_gender' => 'video_url_female']);
        }

        return $next($request);
    }
}
