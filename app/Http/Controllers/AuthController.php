<?php

namespace App\Http\Controllers;

use App\Enums\ApiSlug;
use App\Helpers\Slug;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends BaseController
{

    public function checkMobile(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['mobile' => 'required']);

        $user = User::where('mobile', $request->mobile)->first();

        if ($user) {
            $token = bin2hex(random_bytes(16));
            return $this->success([
                'mobile' => $user->mobile,
                'token' => $token,
            ], Slug::login(ApiSlug::LOGIN_SUCCESS));
        }

        return $this->error('شماره موبایل در سیستم موجود نیست', Slug::login(ApiSlug::NEED_SIGN_IN), 404);
    }


    public function completeProfile(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['mobile' => 'required']);

        $user = User::firstOrCreate(
            ['mobile' => $request->mobile],
            $request->only([
                'first_name', 'last_name', 'birth_date', 'national_code',
                'marital_status', 'children_count', 'spouses_count',
                'province', 'city', 'address'
            ])
        );

        $user->update($request->only([
            'first_name', 'last_name', 'birth_date', 'national_code',
            'marital_status', 'children_count', 'spouses_count',
            'province', 'city', 'address'
        ]));

        $token = bin2hex(random_bytes(16));
        return $this->success([
            'mobile' => $user->mobile,
            'token' => $token,
        ], Slug::login(ApiSlug::LOGIN_SUCCESS));
    }
}
