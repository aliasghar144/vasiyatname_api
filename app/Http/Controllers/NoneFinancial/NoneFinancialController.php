<?php

namespace App\Http\Controllers\NoneFinancial;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\NoneFinancial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoneFinancialController extends BaseController
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $data = NoneFinancial::where('user_id',$user->id)->get(['id', 'person', 'description']);

        if (!$data) {
            return $this->error('طلب یافت نشد', ApiSlug::NONE_FINANCIAL_NOTFOUND->value);
        }

        return $this->success($data,ApiSlug::NONE_FINANCIAL_FOUND->value);
    }

    public function detailsindex($id)
    {
        $user = auth()->user();
        $data = NoneFinancial::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if (!$data) {
            return $this->error('طلب یافت نشد', ApiSlug::CLAIM_NOTFOUND->value);
        }
        return $this->success($data);
    }

    // ایجاد طلب جدید
    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'person' => 'nullable|string|max:255',
            'person_phone' => 'nullable|string|max:13',
            'description' => 'nullable|string',
            'type' => 'in:tohmat,ghyebat,abro,azar',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $data = NoneFinancial::create(array_merge($validator->validated(), [
            'user_id' => $user->id
        ]));

        return $this->success($data, ApiSlug::NONE_FINANCIAL_ADDED->value);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = NoneFinancial::where('id', $id)->where('user_id', $user->id)->first();

        if (!$data) {
            return $this->error(
                'NoneFinancial not found or you do not have permission to update it',
                ApiSlug::NONE_FINANCIAL_UPDATED_FAILED->value,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'person' => 'nullable|string|max:255',
            'person_phone' => 'nullable|string|max:13',
            'type' => 'in:tohmat,ghyebat,abro,azar',
            'description' => 'nullable|string',
            'payed' => 'sometimes|bool',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $data->update($validator->validated());

        return $this->success($data, ApiSlug::NONE_FINANCIAL_UPDATED->value);
    }

    // حذف طلب
    public function destroy($id)
    {
        $user = auth()->user();
        $claim = NoneFinancial::where('id', $id)->where('user_id', $user->id)->first();
        if (!$claim) {
            return $this->error(
                'NoneFinancial not found or you do not have permission to delete it',
                ApiSlug::NONE_FINANCIAL_REMOVE_FAILED->value,
                404
            );
        }
        $claim->delete();

        return $this->success(null, ApiSlug::NONE_FINANCIAL_REMOVED->value,);
    }
}
