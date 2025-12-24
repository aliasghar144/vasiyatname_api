<?php

namespace App\Http\Controllers;


use App\Enums\ApiSlug;
use App\Models\Claim;
use App\Models\Debt;
use App\Models\Fasting;
use App\Models\Khums;
use App\Models\NoneFinancial;
use App\Models\Prayer;
use App\Models\User;
use App\Models\Zakat;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{

    public function completeProfile(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only([
            'first_name',
            'last_name',
            'birth_date',
            'national_code',
            'marital_status',
            'children_count',
            'wife_count',
            'province',
            'city',
            'address',
            'is_married',
            'mobile',
            'email',
            'home_phone',
            'father_name',
            'birth_loc'
        ]);

        // if (!empty($data['birth_date'])) {
        //     // انتظار فرمت: "YYYY/MM/DD" یا "YYYY-MM-DD"
        //     $parts = preg_split('/[\/\-]/', $data['birth_date']);
        //     if (count($parts) !== 3) {
        //         return $this->error('فرمت تاریخ صحیح نیست.', ApiSlug::PROFILE_NOT_FOUND->value, 400);
        //     }
        //     $data['birth_date'] = jalaliToGregorian($parts[0], $parts[1], $parts[2]);
        // }

        $user = auth()->user();

        if (!$user) {
            return $this->error('کاربر احراز هویت نشده است.', ApiSlug::UNAUTHORIZED->value, 401);
        }

        unset($data['mobile']);

        $user->update($data);

        return $this->success($user, ApiSlug::PROFILE_UPDATED->value);
    }

    public function getInformation(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('کاربر یافت نشد', ApiSlug::PROFILE_NOT_FOUND->value);
        }

        $completed = 0;
        $total = 8;
        $nextRoute = null;
        $nextTitle = null;

        // 1️⃣ profile
        $profileCompleted = !empty($user->first_name) && !empty($user->national_code);
        if ($profileCompleted) {
            $completed++;
        } else {
            $nextRoute = '/base/home/profile';
            $nextTitle = 'تکمیل پروفایل';
        }

        // 2️⃣ claims
        if ($nextRoute === null) {
            if (Claim::where('user_id', $user->id)->exists()) {
                $completed++;
            } else {
                $nextRoute = '/base/home/financial/claims';
                $nextTitle = 'افزودن طلب';
            }
        }

        // 3️⃣ dept
        if ($nextRoute === null) {
            if (Debt::where('user_id', $user->id)->exists()) {
                $completed++;
            } else {
                $nextRoute = '/base/home/financial/dept';
                $nextTitle = 'افزودن بدهی';
            }
        }

        // 4️⃣ prayers
        if ($nextRoute === null) {
            if (Prayer::where('user_id', $user->id)->exists()) {
                $completed++;
            } else {
                $nextRoute = '/base/home/religious_duties/prayers';
                $nextTitle = 'افودن نماز قضا';

            }
        }

        // 5️⃣ fasting
        if ($nextRoute === null) {
            if (Fasting::where('user_id', $user->id)->exists()) {
                $completed++;
            } else {
                $nextRoute = '/base/home/religious_duties/fasting';
                $nextTitle = 'افزودن روزه';

            }
        }

        // 6️⃣ khums
        if ($nextRoute === null) {
            if (Khums::where('user_id', $user->id)->exists()) {
                $completed++;
            } else {
                $nextRoute = '/base/home/religious_duties/khums';
                $nextTitle = 'افزودن خمس';

            }
        }

        // 7️⃣ zakat
        if ($nextRoute === null) {
            if (Zakat::where('user_id', $user->id)->exists()) {
                $completed++;
            } else {
                $nextRoute = '/base/home/religious_duties/zakat';
                $nextTitle = 'افزودن زکات';

            }
        }

        // 8️⃣ non financial
        if ($nextRoute === null) {
            if (NoneFinancial::where('user_id', $user->id)->exists()) {
                $completed++;
            } else {
                $nextRoute = '/base/home/non_financial';
                $nextTitle = 'افزودن حق الناس';

            }
        }

        $progress = round($completed / $total, 2);

        return $this->success([
            'user' => $user,
            'progress' => $progress,
            'next_route' => $nextRoute,
            'next_title' => $nextTitle,
            'completed_sections' => $completed,
            'total_sections' => $total,
        ]);
    }


    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->error('کاربر یافت نشد.', ApiSlug::PROFILE_NOT_FOUND->value, 404);
        }

        $user->tokens()->delete();
        $user->fcmToken = null;
        $user->save();

        return $this->success(null, ApiSlug::LOGOUT_SUCCESS->value);
    }

}

function jalaliToGregorian($jy, $jm, $jd): string
{
    // Implementation based on common algorithm
    $jy = (int)$jy;
    $jm = (int)$jm;
    $jd = (int)$jd;
    $jy += 1595;
    $days = -355668 + (365 * $jy) + (int)($jy / 33) * 8 + (int)((($jy % 33) + 3) / 4);
    $months = [0, 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    for ($i = 0; $i < $jm; $i++) $days += $months[$i];
    $days += $jd - 1;
    $gYear = 400 * (int)($days / 146097);
    $days %= 146097;
    if ($days > 36524) {
        $gYear += 100 * (int)(($days - 1) / 36524);
        $days = ($days - 1) % 36524;
        if ($days >= 365) $days++;
    }
    $gYear += 4 * (int)($days / 1461);
    $days %= 1461;
    if ($days > 365) {
        $gYear += (int)(($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    $gYear += (int)($days / 366);
    $days = $days % 366;
    $gMonthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    $isLeap = (($gYear % 4 == 0 && $gYear % 100 != 0) || ($gYear % 400 == 0));
    if ($isLeap) $gMonthDays[1] = 29;
    $gMonth = 0;
    while ($gMonth < 12 && $days >= $gMonthDays[$gMonth]) {
        $days -= $gMonthDays[$gMonth];
        $gMonth++;
    }
    $gDay = $days + 1;
    return sprintf('%04d-%02d-%02d', $gYear, $gMonth + 1, $gDay);
}
