<?php

namespace App\Http\Controllers;

use App\Enums\ApiSlug;
use App\Models\Prayer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AuthController extends BaseController
{

    public function checkMobile(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first('mobile'), ApiSlug::MOBILE_REQUIRED->value, 400);
        }

        $lastOtpTime = Cache::get('otp_time_' . $request->mobile);
        if ($lastOtpTime && Carbon::now()->diffInSeconds($lastOtpTime) < 120) {
            $secondsLeft = 120 - Carbon::now()->diffInSeconds($lastOtpTime);
            return $this->error(
                "لطفا $secondsLeft ثانیه دیگر دوباره تلاش کنید.",
                ApiSlug::OTP_SEND->value,
                429 // HTTP 429 Too Many Requests
            );
        }

        $otp = 1111;
        Cache::put('otp_' . $request->mobile, $otp, Carbon::now()->addMinutes(3));
        //TODO: uncomment below line after put on server
//        Cache::put('otp_time_' . $request->mobile, Carbon::now(), Carbon::now()->addMinutes(2));

        return $this->success([
            'mobile' => $request->mobile,
            //TODO: remove the OTP from the response
            'otp' => $otp
        ], ApiSlug::OTP_SEND->value);

    }

    public function verifyOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|min:10|max:15',
            'otp' => 'required|numeric|digits:4',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::OTP_INVALID->value, 400);
        }

        $cachedOtp = Cache::get('otp_' . $request->mobile);

        if (!$cachedOtp) {
            return $this->error('کد OTP منقضی شده یا وجود ندارد', ApiSlug::OTP_EXPIRED->value, 400);
        }

        if ((int)$request->otp !== (int)$cachedOtp) {
            return $this->error('کد OTP نادرست است', ApiSlug::OTP_INVALID->value, 400);
        }

        // اصلاح: استفاده از first + create به جای firstOrCreate
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            $user = User::create(['mobile' => $request->mobile]);
        }

        Cache::forget('otp_' . $request->mobile);

        $token = $user->createToken('vasiyat_app',['read', 'write'],Carbon::now()->addDays(30))->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], ApiSlug::VERIFIED_SUCCESSFULLY->value);
    }



}
