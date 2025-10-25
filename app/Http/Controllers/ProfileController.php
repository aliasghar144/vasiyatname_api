<?php

namespace App\Http\Controllers;


use App\Enums\ApiSlug;
use App\Models\User;
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
        $data = User::where('id', $user->id)->get();
        if($data){
        return $this->success($data);
        }else{
        return $this->error('کابر یافت نشد',ApiSlug::PROFILE_NOT_FOUND->value);

        }
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
