<?php

namespace App\Http\Controllers;

use App\Enums\ApiSlug;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AuthController extends BaseController
{

    public function checkMobile(Request $request): \Illuminate\Http\JsonResponse
    {
        /// mlipayamak
//
       try {
           $validator = Validator::make($request->all(), [
               'mobile' => 'required|string|min:10|max:15',
           ]);

           if ($validator->fails()) {
               return $this->error($validator->errors()->first('mobile'), ApiSlug::MOBILE_REQUIRED->value, 400);
           }

           $phone = $request->input('mobile');

           $key = "otp_request_{$phone}";
           //todo: decrease mas attempts after test
           $maxAttempts = 3;
           $decayMinutes = 2;

           if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
               $seconds = RateLimiter::availableIn($key);
               return $this->error(
                   message: "Too many attempts. Please try again after $seconds seconds.",
                   slug: 'MANY_TRY',
                   code: 429
               );
           }

           RateLimiter::hit($key, $decayMinutes * 60);

           $url = 'https://console.melipayamak.com/api/send/otp/e1b6742154624626aecce5d76c5399d8';
           $data = ['to' => $phone];
           $response = Http::withoutVerifying()
               ->withHeaders(['Content-Type' => 'application/json'])
               ->post($url, $data);


           if ($response->successful()) {
               $body = $response->json();

               if (!empty($body['code'])) {
                   $otp = substr($body['code'], 0, 4); // گرفتن فقط 4 رقم اول

                   // ذخیره کد در کش
                   Cache::put("otp_{$phone}", $otp, Carbon::now()->addMinutes(5));

                   return $this->success([
                       'mobile' => $request->mobile,
                       //TODO: remove the OTP from the response
//                       'otp' => $otp
                   ], ApiSlug::OTP_SEND->value);
               }

               $this->error('$validator->errors()->first()', 'OTP_NOT_RETURNED');
           }

           return $this->error(
               json_encode($response->json()), // Convert array to string
               'SMS_API_FAILED',
               500,
           );
       } catch (ValidationException $e) {
           return $this->error(
               'Validation failed',
               '$e->errors()',
               422
           );
       } catch (\Exception $e) {
           return $this->error(
               'Error sending OTP',
               $e->getMessage(),
               500
           );
       }

        // $validator = Validator::make($request->all(), [
        //     'mobile' => 'required|string|min:10|max:15',
        // ]);

        // if ($validator->fails()) {
        //     return $this->error($validator->errors()->first('mobile'), ApiSlug::MOBILE_REQUIRED->value, 400);
        // }

        // $lastOtpTime = Cache::get('otp_time_' . $request->mobile);

//        if ($lastOtpTime && Carbon::now()->diffInSeconds($lastOtpTime) < 120) {
//            $secondsLeft = 120 - Carbon::now()->diffInSeconds($lastOtpTime);
//            return $this->error(
//                "لطفا $secondsLeft ثانیه دیگر دوباره تلاش کنید.",
//                ApiSlug::OTP_SEND->value,
//                429 // HTTP 429 Too Many Requests
//            );
//        }

//        $otp = 1111;
//        Cache::put('otp_' . $request->mobile, $otp, Carbon::now()->addMinutes(3));
//        TODO: uncomment below line after put on server
//       Cache::put('otp_time_' . $request->mobile, Carbon::now(), Carbon::now()->addMinutes(2));

//        return $this->success([
//            'mobile' => $request->mobile,
//            //TODO: remove the OTP from the response
//            // 'otp' => $otp
//        ], ApiSlug::OTP_SEND->value);

    }

    public function verifyOtp(Request $request): \Illuminate\Http\JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|min:10|max:15',
            'otp' => 'required|numeric|digits:4',
            'fcmToken' => 'string',
            'app_version' => 'string',
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

        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            $user = User::create(['mobile' => $request->mobile,
                'last_seen_at' => Carbon::now(),
                'fcmToken' => $request->fcmToken,
                'app_version' => $request->app_version ?? '101',
            ]);
        }

        Cache::forget('otp_' . $request->mobile);

        $token = $user->createToken('vasiyat_app', ['read', 'write'], Carbon::now()->addDays(30))->plainTextToken;

        $user = User::where('mobile', $request->mobile)->first();

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], ApiSlug::VERIFIED_SUCCESSFULLY->value);
    }


    function verifycode(Request $request){
        try {
            $request->validate([
                'mobile' => 'required|string|min:10|max:15',
                'otp' => 'required|numeric|digits:4',
                'fcmToken' => 'string',
                'app_version' => 'string',
            ]);

            $phone = $request->input('phone');
            $otp = $request->input('otp');

            $attemptKey = "otp_attempts_{$phone}";
            $maxAttempts = 5;
            $decaySeconds = 300; // 5 minutes

            if (RateLimiter::tooManyAttempts($attemptKey, $maxAttempts)) {
                return $this->error(
                    'Too many incorrect attempts. Please request a new OTP.',
                    ApiSlug::TOO_MANY_ATTEMPTS->value,
                    429
                );
            }

            $cachedOtp = Cache::get("otp_{$phone}");

            // Check if the OTP exists and matches
            if (!$cachedOtp) {
                return $this->error(
                    'OTP expired',
                    ApiSlug::OTP_EXPIRED->value
                );
            }

            // Check if the OTP matches
            if ($cachedOtp !== $otp) {
                return $this->error(
                    'Invalid OTP',
                    ApiSlug::OTP_INVALID->value
                );
            }

            // OTP is valid, clear it from the cache
            Cache::forget("otp_{$phone}");

            $user = User::where('mobile', $request->mobile)->first();
            if (!$user) {
                $user = User::create(['mobile' => $request->mobile,
                    'last_seen_at' => Carbon::now(),
                    'fcmToken' => $request->fcmToken,
                    'app_version' => $request->app_version ?? '101',
                ]);
            }

            $token = $user->createToken('vasiyat_app', ['read', 'write'], Carbon::now()->addDays(30))->plainTextToken;

            $user = User::where('mobile', $request->mobile)->first();

            return $this->success([
                'user' => $user,
                'token' => $token,
            ], ApiSlug::VERIFIED_SUCCESSFULLY->value);

        } catch (ValidationException $e) {
            return $this->error(
                'Validation failed',
                ApiSlug::VALIDATION_ERROR->value,
                422
            );
        } catch (\Exception $e) {
            return $this->error(
                'Error verifying OTP',
                $e->getMessage(),
                500,
            );
        }
    }

}
