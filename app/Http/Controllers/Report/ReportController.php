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
use App\Models\RecAndReq;
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


        $claimsFinancialSum = Claim::where('user_id', $user->id)->where('claim_type', 'financial')->whereNotIn('status', ['received'])->sum('amount');
        $claimsFinancial = Claim::where('user_id', $user->id)->where('claim_type', 'financial')->whereNotIn('status', ['received'])->get(['person', 'person_phone','amount']);

        $claimsNonFinancialSum = Claim::where('user_id', $user->id)->where('claim_type', 'none_financial')->whereNotIn('status', ['received'])->count();
        $claimsNonFinancial = Claim::where('user_id', $user->id)->where('claim_type', 'none_financial')->whereNotIn('status', ['received'])->get(['person', 'person_phone','amount']);

        $debtMardomiSum = Debt::where('user_id', $user->id)->where('debt_type', 'mardomi')->sum('amount');
        $debtMardomi = Debt::where('user_id', $user->id)->where('debt_type', 'mardomi')->get(['person', 'person_phone','amount']);

        $debtBankiSum = Debt::where('user_id', $user->id)->where('debt_type', 'banki',)->count();
        $recAndReq = RecAndReq::where('user_id', $user->id)->first();

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

        $fasting = $fastingRow?->fasting_rec ?? 0;
        $fastingRec = $fastingRow?->fasting ?? 0;

        $khumsSum = Khums::where('user_id', $user->id)->where('payed',false)->sum('amount');
        $zakatSum = Zakat::where('user_id', $user->id)->where('payed',false)->sum('amount');


        $noneFinancialTohmatCount = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'tohmat')->count();
        $noneFinancialTohmatUsers = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'tohmat')->get(['person', 'person_phone']);

        $noneFinancialGheybatCount = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'ghyebat')->count();
        $noneFinancialGheybatUsers = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'ghyebat')->get(['person', 'person_phone']);

        $noneFinancialAbroCount = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'abro')->count();
        $noneFinancialAbroUsers = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'abro')->get(['person', 'person_phone']);

        $noneFinancialAzarCount = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'azar')->count();
        $noneFinancialAzarUsers = NoneFinancial::where('user_id', $user->id)->where('payed',false)->where('type', 'azar')->get(['person', 'person_phone']);

        $prayer = Prayer::where('user_id', $user->id)->first();

        return $this->success([
            'financial' => [
                'claims_financial_total_price' => $claimsFinancialSum,
                'claims_financial_user_list' => $claimsFinancial,
                'claims_none_financial_total_price' => $claimsNonFinancialSum,
                'claims_none_financial_user_list' => $claimsNonFinancial,
                'debt_banki' => $debtBankiSum,
                'debt_mardomi_total_price' => $debtMardomiSum,
                'debt_mardomi_user_list' => $debtMardomi,
            ],
            'religious_duties' => [
                'prayers' => [
                    'pray_details' => [
                        'fajr' => $prayer?->fajr_prayer ?? 0,
                        'dhuhr_prayer' => $prayer?->dhuhr_prayer ?? 0,
                        'asr_prayer' => $prayer?->asr_prayer ?? 0,
                        'maghrib_prayer' => $prayer?->maghrib_prayer ?? 0,
                        'isha_prayer' => $prayer?->isha_prayer ?? 0,

                        'fajr_prayer_rec' => $prayer?->fajr_prayer_rec ?? 0,
                        'dhuhr_prayer_rec' => $prayer?->dhuhr_prayer_rec ?? 0,
                        'asr_prayer_rec' => $prayer?->asr_prayer_rec ?? 0,
                        'maghrib_prayer_rec' => $prayer?->maghrib_prayer_rec ?? 0,
                        'isha_prayer_rec' => $prayer?->isha_prayer_rec ?? 0,
                    ],
                    'total_daily' => $dailyPrayers,
                    'total_qadha' => $recPrayers,
                    'total_ayat' => $ayatPrayers,
                ],
                'fasting' => $fasting,
                'fasting_req' => $fastingRec,
                'khums_sum' => $khumsSum,
                'zakat_sum' => $zakatSum,
            ],

            'none_financial' => [
                'tohmat' => $noneFinancialTohmatCount,
                'tohmat_users' => $noneFinancialTohmatUsers,
                'ghyebat' => $noneFinancialGheybatCount,
                'ghyebat_users' => $noneFinancialGheybatUsers,
                'abro' => $noneFinancialAbroCount,
                'abro_users' => $noneFinancialAbroUsers,
                'azar' => $noneFinancialAzarCount,
                'azar_users' => $noneFinancialAzarUsers,
            ],
            'recAndReq' => [
                'rec' => $recAndReq->req_description ?? '',
                'req' => $recAndReq->type_ceremony_des ?? '',
            ]

        ]);
    }

}
