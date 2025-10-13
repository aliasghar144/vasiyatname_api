<?php

namespace  App\Http\Controllers\Religious;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Fasting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FastingController extends BaseController
{
    public function index()
    {
        $user = auth()->user();

        // یک ردیف برای هر کاربر کافی است
        $fasting = Fasting::where('user_id', $user->id)->first();

        // اگر هنوز رکوردی وجود ندارد، ایجاد کن
        if (!$fasting) {
            $fasting = Fasting::create([
                'user_id' => $user->id,
                'fasting' => 0,
                'fasting_rec' => 0,
            ]);
        }


        $data = [
            'fasting' => ['id' => 0, 'title' => 'روزه قضا قطعی',     'value' => $fasting->fasting],
            'fasting_rec' => ['id' => 1, 'title' => 'روزه قضا غیر قطعی',     'value' => $fasting->fasting_rec],

        ];

        return $this->success($data);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'fasting'       => 'sometimes|integer|min:0',
            'fasting_rec'      => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::PRAYER_UPDATE_FAILED->value, 422);
        }

        $prayer = Fasting::firstOrCreate(['user_id' => $user->id]);

        $prayer->update($validator->validated());

        $data = [
            'prayers' => [
                ['id' => 0, 'title' => 'روزه قضا قطعی',     'value' => $prayer->fajr_prayer],
                ['id' => 1, 'title' => 'روزه قضا غیر قطعی',     'value' => $prayer->dhuhr_prayer],
            ],
        ];

        return $this->success($data, ApiSlug::PRAYER_UPDATED->value);
    }
}
