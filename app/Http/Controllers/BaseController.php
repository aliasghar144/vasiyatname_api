<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    // پاسخ موفق
    protected function success($data = null, string $slug = 'success', int $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'slug' => $slug,
            'message' => null,
            'data' => $data
        ], $code);
    }

    // پاسخ خطا
    protected function error(string $message = 'Error', string $slug = 'error', int $code = 400, $data = null): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'slug' => $slug,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
