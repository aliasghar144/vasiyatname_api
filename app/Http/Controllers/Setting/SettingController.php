<?php

namespace App\Http\Controllers\Setting;


use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

            if ($data) {
                return $this->success([], 'FEEDBACK_STORED');
            } else {
                return $this->error('خطا در برقراری ارتباط با سرور', ApiSlug::DATABASE_ERROR->value);
            }
        } catch (\Exception $e) {
            return $this->error('خطا در برقراری ارتباط با سرور', ApiSlug::DATABASE_ERROR->value);
        }
    }

    public function updateNotificationSetting(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'show_notif' => 'required|boolean',
                'reminder_interval' => 'required|in:oneWeek,oneMonth,twoMonth,thereMonth,sixMonth',
            ]);

            if ($validator->fails()) {
                return $this->error(
                    $validator->errors()->first(),
                    'VALIDATE_ERROR'
                );
            }

            $user = auth()->user();

            if (!$user) {
                return $this->error(
                    'کاربر احراز هویت نشده است',
                    'UNAUTHORIZED'
                );
            }

            $data = $validator->validated();

            // اگر نوتیف خاموش شد، تاریخ ارسال reminder ریست شود
            if (isset($data['show_notif']) && $data['show_notif'] === false) {
                $data['last_reminder_sent_at'] = null;
            }

            $user->update($data);
            $user->refresh();

            return $this->success([
                'isShow' => (bool) $user->show_notif,
                'reminder_interval' => $user->reminder_interval,
            ], 'NOTIFICATION_SETTING_UPDATED');

        } catch (\Exception $e) {
            Log::error('Update notification setting failed', [
                'exception' => $e,
            ]);

            return $this->error(
                'خطا در برقراری ارتباط با سرور',
                ApiSlug::DATABASE_ERROR->value
            );
        }
    }
}
