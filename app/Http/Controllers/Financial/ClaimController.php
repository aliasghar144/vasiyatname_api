<?php

namespace App\Http\Controllers\Financial;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClaimController extends BaseController
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $financial = Claim::where('user_id',$user->id)->where('claim_type', 'financial')->get(['id', 'from', 'amount']);
        $noneFinancial = Claim::where('user_id',$user->id)->where('claim_type', 'none_financial')->get(['id', 'from', 'description']);
        $claim = Claim::where('user_id', $user->id)->get(['id', 'from', 'amount']);

        if (!$financial || !$noneFinancial) {
            return $this->error('طلب یافت نشد', ApiSlug::CLAIM_NOTFOUND->value);
        }

        return $this->success([
            'noneFinancial' => $noneFinancial,
            'financial' => $financial,
        ]);
    }

    public function detailsindex($id)
    {
        $user = auth()->user();
        $claim = Claim::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if (!$claim) {
            return $this->error('طلب یافت نشد', ApiSlug::CLAIM_NOTFOUND->value);
        }
        return $this->success($claim);
    }

    // ایجاد طلب جدید
    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'from' => 'required|string|max:255',
            'amount' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'in:pending,received',
            'claim_type' => 'in:financial,none_financial',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $claim = Claim::create(array_merge($validator->validated(), [
            'user_id' => $user->id
        ]));

        return $this->success($claim, ApiSlug::CLAIM_ADDED->value);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $claim = Claim::where('id', $id)->where('user_id', $user->id)->first();

        if (!$claim) {
            return $this->error(
                'Claim not found or you do not have permission to update it',
                ApiSlug::CLAIM_UPDATED_FAILED->value,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'from' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|string',
            'status' => 'in:pending,received',
            'claim_type' => 'in:financial,none_financial',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $claim->update($validator->validated());

        return $this->success($claim, ApiSlug::CLAIM_UPDATED->value);
    }

    // حذف طلب
    public function destroy($id)
    {
        $user = auth()->user();
        $claim = Claim::where('id', $id)->where('user_id', $user->id)->first();
        if (!$claim) {
            return $this->error(
                'Claim not found or you do not have permission to delete it',
                ApiSlug::PRAYER_REMOVE_FAILED->value,
                404
            );
        }
        $claim->delete();

        return $this->success(null, ApiSlug::CLAIM_REMOVED->value,);
    }
}
