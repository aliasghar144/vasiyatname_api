<?php

namespace App\Http\Controllers\Report;

use App\Enums\ApiSlug;
use App\Models\Claim;
use App\Models\RecAndReq;
use Illuminate\Http\Request;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class WillTextController extends BaseController
{

    public function recAndReqGet(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $claim = RecAndReq::where('user_id', $user->id)
            ->first();
        if (!$claim) {
            return $this->error('توصیه یافت نشد', ApiSlug::REC_AND_REQ_NOT_FOUND->value);
        }
        return $this->success($claim);
    }

    public function recAndReqStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('کاربر یافت نشد', ApiSlug::PROFILE_NOT_FOUND->value);
        }

        $validator = Validator::make($request->all(), [
            'req_description' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                ApiSlug::VALIDATION_ERROR->value,
                400
            );
        }

        $recAndReq = RecAndReq::updateOrCreate(
            ['user_id' => $user->id],
            $validator->validated()
        );

        return $this->success(
            $recAndReq,
            ApiSlug::REC_AND_REQ_ADD->value
        );
    }


    public function typeOfCeremonyStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('کاربر یافت نشد', ApiSlug::PROFILE_NOT_FOUND->value);
        }

        $validator = Validator::make($request->all(), [
            'type_ceremony_des' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error(
                $validator->errors()->first(),
                ApiSlug::VALIDATION_ERROR->value,
                400
            );
        }

        $recAndReq = RecAndReq::updateOrCreate(
            ['user_id' => $user->id],
            $validator->validated()
        );

        return $this->success(
            $recAndReq,
            ApiSlug::REC_AND_REQ_ADD->value
        );
    }

}
