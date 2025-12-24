<?php

namespace App\Http\Controllers\Setting;


use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\AppSetting;
use App\Models\ContactUs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SettingController extends BaseController
{

    public function sendFeedBack(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), "VALIDATE_ERROR");
            }

            $user = auth()->user();

            if (!$user) {
                return $this->error('کاربر احراز هویت نشده است', 'UNAUTHORIZED');
            }

            $data = ContactUs::create([
                'user_id' => $user->id,
                'subject' => $request->subject,
                'description' => $request->description,
            ]);

            if($data){
                return $this->success([], 'FEEDBACK_STORED');
            }else{
                return $this->error('خطا در برقراری ارتباط با سرور', ApiSlug::DATABASE_ERROR->value);
            }
        } catch (\Exception $e) {
            return $this->error('خطا در برقراری ارتباط با سرور', ApiSlug::DATABASE_ERROR->value);
        }
    }
}
