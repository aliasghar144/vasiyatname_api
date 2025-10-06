<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Enums\ApiSlug;

class SanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'code' => 401,
                'slug' => ApiSlug::UNAUTHORIZED->value,
                'message' => 'عدم دسترسی',
            ], 401);
        }

        $token = substr($header, 7);
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'code' => 401,
                'slug' => ApiSlug::UNAUTHORIZED->value,
                'message' => 'عدم دسترسی',
            ], 401);
        }

        // چک انقضا در صورتی که استفاده میکنید
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json(['message' => 'Token expired'], 401);
        }

        // ست کردن کاربر روی auth
        auth()->setUser($accessToken->tokenable);

        return $next($request);
    }
}
