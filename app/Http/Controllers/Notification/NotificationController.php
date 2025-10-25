<?php

namespace App\Http\Controllers\Notification;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Notifications;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class NotificationController extends BaseController
{
    protected $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }

    public function sendToUser(Request $request)
    {
        $userId = $request->input('user_id');
        $title = $request->input('title');
        $body  = $request->input('body');
        $data  = $request->input('data', []);

        Notifications::create([
            'user_id' => $userId,
            'title' => $title,
            'body'  => $body,
            'data'  => $data,
            'read'  => false,
        ]);

        $this->fcm->sendToUser($userId, $title, $body, $data);

        return response()->json(['success' => true]);
    }


    //  لیست نوتیف‌ها برای کاربر
    public function list(Request $request)
    {
        $user = auth()->user();
        $notifications = Notifications::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if(!$notifications){
            return $this->error(ApiSlug::NOTIFICATION_NOTFOUND->value);
        }

        return $this->success($notifications, ApiSlug::NOTIFICATION_FOUND->value);
    }


    //  علامت‌گذاری خوانده شده
    public function markAsRead(Request $request, $id)
    {
        $user = auth()->user();
        $notification = Notifications::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $notification->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    //اپدیت وضعیت نمایش نوتیفیکیشن
    public function updateNotifState(Request $request){

        $validator = Validator::make($request->all(), [
            'show_notif' => 'required|bool',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first('mobile'), ApiSlug::MOBILE_REQUIRED->value, 400);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error('کاربر احراز هویت نشده است.', ApiSlug::UNAUTHORIZED->value, 401);
        }


        $user->update($validator->validated());

        return $this->success($user, ApiSlug::NOTIFICATION_STATE->value);
    }
}
