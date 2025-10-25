<?php

namespace App\Http\Controllers;


use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class AppSettingController extends BaseController
{
    public function appVersion()
    {
        try {
            $settings = AppSetting::first();

            if (!$settings) {
                return $this->error('خطا در برقراری ارتباط با سرور',ApiSlug::DATABASE_ERROR->value);

            }

            return response()->json([
                'app_version' => $settings->app_version,
                'force_version' => $settings->force_version,
            ]);
        } catch (\Exception $e) {
            return $this->error('خطا در برقراری ارتباط با سرور',ApiSlug::DATABASE_ERROR->value);
        }
    }
}
