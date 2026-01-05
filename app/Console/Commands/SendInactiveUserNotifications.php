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

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        $this->fcm = new \App\Services\FcmService();

        echo "Start sending reminder notifications...\n";

        $users = User::where('show_notif', true)
            ->whereNotNull('last_seen_at')
            ->get();

        foreach ($users as $user) {

            if (!$user->fcmToken) {
                continue;
            }

            // تعیین interval
            switch ($user->reminder_interval) {
                case 'test':
                    $interval = Carbon::now()->subMinutes(2);
                    break;

                case 'oneWeek':
                    $interval = Carbon::now()->subWeek();
                    break;

                case 'oneMonth':
                    $interval = Carbon::now()->subMonth();
                    break;

                case 'twoMonth':
                    $interval = Carbon::now()->subMonths(2);
                    break;

                case 'thereMonth':
                    $interval = Carbon::now()->subMonths(3);
                    break;

                case 'sixMonth':
                    $interval = Carbon::now()->subMonths(6);
                    break;

                default:
                    continue 2; // interval نامعتبر
            }

            // تاریخ مرجع برای مقایسه
            $referenceTime = $user->last_reminder_sent_at ?? $user->last_seen_at;

            // هنوز موعد ارسال نشده
            if (Carbon::parse($referenceTime)->gt($interval)) {
                continue;
            }

            try {
                Notifications::create([
                    'user_id' => $user->id,
                    'title' => 'سلام دوباره!',
                    'body' => 'مدتی هست که به اپ سر نزدی، منتظرت هستیم 🌱',
                    'read' => false,
                    'data' => ['type' => 'reminder'],
                ]);

                // بروزرسانی زمان آخرین ارسال reminder
                $user->update([
                    'last_reminder_sent_at' => $now,
                ]);

                $this->fcm->send(
                    $user->fcmToken,
                    'سلام دوباره!',
                    'مدتی هست که به اپ سر نزدی',
                    ['type' => 'reminder']
                );

                echo "Reminder sent to user {$user->id}";

            } catch (\Exception $e) {
                Log::error('Reminder failed', [
                    'user_id' => $user->id,
                    'exception' => $e
                ]);
            }
        }

        $this->info('Finished sending reminders.');
    }
}
