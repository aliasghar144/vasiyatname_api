<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Notifications;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInactiveUserNotifications extends Command
{
    protected $signature = 'notify:inactive-users';
    protected $description = 'ارسال نوتیف به کاربرانی که مدتی است به اپ سر نزده‌اند';

    protected $fcm;

    public function __construct(FcmService $fcm)
    {
        parent::__construct();
        $this->fcm = $fcm;
    }

    public function handle()
    {
        $now = Carbon::now();

        $this->info('Start sending inactive user notifications...');


        // پیدا کردن کاربران غایب
        $threshold = Carbon::now()->subMonth();
        $users = User::where('show_notif', true)->where('last_seen_at', '<', $threshold)->get();

        if ($users->isEmpty()) {
            $this->info('No inactive users found.');
            return;
        }

        foreach ($users as $user) {
            // اگر توکن FCM ندارند رد شود
            if (!$user->fcmToken) {
                $this->info("User ID {$user->id} has no FCM token. Skipped.");
                continue;
            }

            // ذخیره نوتیف در DB
            try {
                Notifications::create([
                    'user_id' => $user->id,
                    'title'   => 'سلام دوباره!',
                    'body'    => 'مدتی هست که به اپ سر نزدی، بیا دوباره!',
                    'read'    => false,
                    'data'    => ['type' => 'reminder']
                ]);
                User::where('id', $user->id)->update(['last_seen_at' => Carbon::now()->subDays(10)]);
            } catch (\Exception $e) {
                $this->error("Failed to save notification for user {$user->id}: {$e->getMessage()}");
                Log::error("Failed to save notification for user {$user->id}", ['exception' => $e]);
                continue;
            }

            // ارسال نوتیف با FCM
            try {
                $this->fcm->send(
                    $user->fcmToken,
                    'سلام دوباره!',
                    'مدتی هست که به اپ سر نزدی',
                    ['type' => 'reminder']
                );
                $this->info("Notification sent to user ID {$user->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send notification to user {$user->id}: {$e->getMessage()}");
                Log::error("FCM send failed for user {$user->id}", ['exception' => $e]);
            }
        }

        $this->info('Finished sending notifications.');
    }
}
