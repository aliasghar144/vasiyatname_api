<?php

namespace App\Http\Controllers\Report;

use App\Enums\ApiSlug;
use App\Models\Claim;
use App\Models\RecAndReq;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class WillTextController extends BaseController
{

    public function recAndReqGet(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        $claim = RecAndReq::where('user_id', $user->id)->first();

        if (!$claim) {
            return $this->success([
                'id' => 0,
                'user_id' => $user->id,
                'req_description' => '',
                'type_ceremony_des' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ], ApiSlug::REC_AND_REQ_NOT_FOUND->value);
        }

        return $this->success([
            'id' => $claim->id,
            'user_id' => $claim->user_id,
            'req_description' => $claim->req_description,
            'type_ceremony_des' => $claim->type_ceremony_des,
            'created_at' => $claim->created_at,
            'updated_at' => $claim->updated_at,
        ]);
    }

    public function recAndReqStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error('کاربر یافت نشد', ApiSlug::PROFILE_NOT_FOUND->value);
        }

        $validator = Validator::make($request->all(), [
            'req_description' => 'nullable|string',
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
            [
                'req_description' => $request->filled('req_description')
                    ? $request->req_description
                    : null
            ]
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
            'type_ceremony_des' => 'nullable|string',
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
            [
                'type_ceremony_des' => $request->filled('type_ceremony_des')
                    ? $request->type_ceremony_des
                    : null
            ]
        );

        return $this->success(
            $recAndReq,
            ApiSlug::REC_AND_REQ_ADD->value
        );
    }

}
