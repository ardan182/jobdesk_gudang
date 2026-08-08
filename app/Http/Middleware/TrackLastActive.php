<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackLastActive
{
    public const ONLINE_LIFETIME_MINUTES = 10;

    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if ($user) {
            Cache::put('user-online-' . $user->id, true, now()->addMinutes(self::ONLINE_LIFETIME_MINUTES));
        }

        return $next($request);
    }
}