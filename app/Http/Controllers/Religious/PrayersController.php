<?php

namespace  App\Http\Controllers\Religious;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Prayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrayersController extends BaseController
{
    public function index()
    {
        $user = auth()->user();

        // یک ردیف برای هر کاربر کافی است
        $prayer = Prayer::where('user_id', $user->id)->first();

        // اگر هنوز رکوردی وجود ندارد، ایجاد کن
        if (!$prayer) {
            $prayer = Prayer::create([
                'user_id' => $user->id,
                'fajr_prayer' => 0,
                'dhuhr_prayer' => 0,
                'asr_prayer' => 0,
                'maghrib_prayer' => 0,
                'isha_prayer' => 0,
                'fajr_prayer_rec' => 0,
                'dhuhr_prayer_rec' => 0,
                'asr_prayer_rec' => 0,
                'maghrib_prayer_rec' => 0,
                'isha_prayer_rec' => 0,
                'ayat' => 0,
                'ayat_rec' => 0,
            ]);
        }


          $data = [
        'prayers' => [
            ['id' => 0, 'title' => 'نماز صبح',     'value' => $prayer->fajr_prayer],
            ['id' => 1, 'title' => 'نماز ظهر',     'value' => $prayer->dhuhr_prayer],
            ['id' => 2, 'title' => 'نماز عصر',     'value' => $prayer->asr_prayer],
            ['id' => 3, 'title' => 'نماز مغرب',    'value' => $prayer->maghrib_prayer],
            ['id' => 4, 'title' => 'نماز عشاء',    'value' => $prayer->isha_prayer],
        ],
        'prayers_rec' => [
            ['id' => 5, 'title' => 'نماز صبح',  'value' => $prayer->fajr_prayer_rec],
            ['id' => 6, 'title' => 'نماز ظهر',  'value' => $prayer->dhuhr_prayer_rec],
            ['id' => 7, 'title' => 'نماز عصر',  'value' => $prayer->asr_prayer_rec],
            ['id' => 8, 'title' => 'نماز مغرب', 'value' => $prayer->maghrib_prayer_rec],
            ['id' => 9, 'title' => 'نماز عشاء', 'value' => $prayer->isha_prayer_rec],
        ],
        'prayers_ayat' => [
            'id' => 10,
            'title' => 'نماز آیات قطعی',
            'value' => $prayer->ayat,
        ],
        'prayers_ayat_rec' => [
            'id' => 11,
            'title' => 'نماز آیات غیر قطعی',
            'value' => $prayer->ayat_rec,
        ],
    ];

        return $this->success($data);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'fajr_prayer'       => 'sometimes|integer|min:0',
            'dhuhr_prayer'      => 'sometimes|integer|min:0',
            'asr_prayer'        => 'sometimes|integer|min:0',
            'maghrib_prayer'    => 'sometimes|integer|min:0',
            'isha_prayer'       => 'sometimes|integer|min:0',
            'fajr_prayer_rec'   => 'sometimes|integer|min:0',
            'dhuhr_prayer_rec'  => 'sometimes|integer|min:0',
            'asr_prayer_rec'    => 'sometimes|integer|min:0',
            'maghrib_prayer_rec' => 'sometimes|integer|min:0',
            'isha_prayer_rec'   => 'sometimes|integer|min:0',
            'ayat'              => 'sometimes|integer|min:0',
            'ayat_rec'          => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::PRAYER_UPDATE_FAILED->value, 422);
        }

        $prayer = Prayer::firstOrCreate(['user_id' => $user->id]);

        $prayer->update($validator->validated());

                  $data = [
        'prayers' => [
            ['id' => 0, 'title' => 'نماز صبح',     'value' => $prayer->fajr_prayer],
            ['id' => 1, 'title' => 'نماز ظهر',     'value' => $prayer->dhuhr_prayer],
            ['id' => 2, 'title' => 'نماز عصر',     'value' => $prayer->asr_prayer],
            ['id' => 3, 'title' => 'نماز مغرب',    'value' => $prayer->maghrib_prayer],
            ['id' => 4, 'title' => 'نماز عشاء',    'value' => $prayer->isha_prayer],
        ],
        'prayers_rec' => [
            ['id' => 5, 'title' => 'نماز صبح',  'value' => $prayer->fajr_prayer_rec],
            ['id' => 6, 'title' => 'نماز ظهر',  'value' => $prayer->dhuhr_prayer_rec],
            ['id' => 7, 'title' => 'نماز عصر',  'value' => $prayer->asr_prayer_rec],
            ['id' => 8, 'title' => 'نماز مغرب', 'value' => $prayer->maghrib_prayer_rec],
            ['id' => 9, 'title' => 'نماز عشاء', 'value' => $prayer->isha_prayer_rec],
        ],
        'prayers_ayat' => [
            'id' => 10,
            'title' => 'نماز آیات قطعی',
            'value' => $prayer->ayat,
        ],
        'prayers_ayat_rec' => [
            'id' => 11,
            'title' => 'نماز آیات غیر قطعی',
            'value' => $prayer->ayat_rec,
        ]]
        ;

        return $this->success($data, ApiSlug::PRAYER_UPDATED->value);
    }
}
