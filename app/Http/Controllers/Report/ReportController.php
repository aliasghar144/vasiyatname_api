<?php

namespace App\Http\Controllers\Report;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Claim;
use App\Models\Debt;
use App\Models\Fasting;
use App\Models\Khums;
use App\Models\NoneFinancial;
use App\Models\Prayer;
use App\Models\Zakat;
use Illuminate\Http\Request;

class ReportController extends BaseController
{

    public function get(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('کاربر یافت نشد', ApiSlug::PROFILE_NOT_FOUND->value);
        }

        $claimsFinancialSum = Claim::where('user_id', $user->id)->where('claim_type', 'financial')->sum('amount');
        $claimsNonFinancialSum = Claim::where('user_id', $user->id)->where('claim_type', 'none_financial')->count();
        $debtMardomiSum = Debt::where('user_id', $user->id)->where('debt_type', 'mardomi')->sum('amount');
        $debtBankiSum = Debt::where('user_id', $user->id)->where('debt_type', 'banki')->count();

        $prayerTotals = Prayer::where('user_id', $user->id)
            ->selectRaw('
        COALESCE(SUM(fajr_prayer), 0) +
        COALESCE(SUM(dhuhr_prayer), 0) +
        COALESCE(SUM(asr_prayer), 0) +
        COALESCE(SUM(maghrib_prayer), 0) +
        COALESCE(SUM(isha_prayer), 0) as daily_prayers,

        COALESCE(SUM(fajr_prayer_rec), 0) +
        COALESCE(SUM(dhuhr_prayer_rec), 0) +
        COALESCE(SUM(asr_prayer_rec), 0) +
        COALESCE(SUM(maghrib_prayer_rec), 0) +
        COALESCE(SUM(isha_prayer_rec), 0) as rec_prayers,

        COALESCE(SUM(ayat), 0) +
        COALESCE(SUM(ayat_rec), 0) as ayat_prayers
    ')
            ->first();

        $dailyPrayers = (int)$prayerTotals->daily_prayers;
        $recPrayers = (int)$prayerTotals->rec_prayers;
        $ayatPrayers = (int)$prayerTotals->ayat_prayers;

        $fastingRow = Fasting::where('user_id', $user->id)->first();

        $fasting = $fastingRow?->fasting ?? 0;
        $fastingRec = $fastingRow?->fasting_rec ?? 0;

        $khumsSum = Khums::where('user_id', $user->id)->sum('amount');
        $zakatSum = Zakat::where('user_id', $user->id)->sum('amount');


        $noneFinancialTohmatCount = NoneFinancial::where('user_id', $user->id)->where('type', 'tohmat')->count();
        $noneFinancialGheybatCount = NoneFinancial::where('user_id', $user->id)->where('type', 'ghyebat')->count();
        $noneFinancialAbroCount = NoneFinancial::where('user_id', $user->id)->where('type', 'abro')->count();
        $noneFinancialAzarCount = NoneFinancial::where('user_id', $user->id)->where('type', 'azar')->count();


        return $this->success([
            'financial' => [
                'claims_financial' => $claimsFinancialSum,
                'claims_none_financial' => $claimsNonFinancialSum,
                'debt_banki' => $debtBankiSum,
                'debt_mardomi' => $debtMardomiSum,
            ],
            'religious_duties' => [
                'prayers' => [
                    'daily' => $dailyPrayers,
                    'qadha' => $recPrayers,
                    'ayat' => $ayatPrayers,
                ],
                'fasting' => $fasting,
                'fasting_req' => $fastingRec,
                'khums_sum' => $khumsSum,
                'zakat_sum' => $zakatSum,
            ],

            'none_financial' => [
                'tohmat' => $noneFinancialTohmatCount,
                'ghyebat' => $noneFinancialGheybatCount,
                'abro' => $noneFinancialAbroCount,
                'azar' => $noneFinancialAzarCount,
            ],
        ]);
    }

}
