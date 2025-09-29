<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ThrottleRequests
{
    // تعداد درخواست مجاز در مدت زمان مشخص (مثلاً 60 ثانیه)
    //TODO decrease max Attempts after upload on host
    protected int $maxAttempts = 60;
    protected int $decaySeconds = 60;

    public function handle($request, Closure $next)
    {
        // کلید یکتا برای هر IP
        $key = 'rate_limit_' . $request->ip();

        $attempts = Cache::get($key, 0);

        if ($attempts >= $this->maxAttempts) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }

        // افزایش شمارنده و تنظیم زمان انقضا
        Cache::put($key, $attempts + 1, Carbon::now()->addSeconds($this->decaySeconds));

        return $next($request);
    }
}
