<?php

namespace App\Http\Controllers\Financial;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Debt;

class DebtController extends BaseController
{
    public function index()
    {
        $user = auth()->user();

        $mardomi = Debt::where('user_id',$user->id)->where('debt_type', 'mardomi')->get(['id', 'from', 'amount']);
        $banki = Debt::where('user_id',$user->id)->where('debt_type', 'banki')->get(['id', 'bank_name', 'amount']);

        return $this->success([
            'mardomi' => $mardomi,
            'banki' => $banki,
        ]);
    }

    public function detailsindex($id)
    {
        $user = auth()->user();
        $debt = Debt::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if (!$debt) {
            return $this->error('طلب یافت نشد', ApiSlug::DEBT_NOTFOUND->value);
        }
        return $this->success($debt,ApiSlug::DEBT_FOUND->value);
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $validator = Validator::make($request->all(), [
                'from' => 'string',
                'debt_type' => 'required|in:mardomi,banki',
                'bank_name' => 'string',
                'description' => 'string',
                'amount' => 'integer',
                'due_date' => 'date',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
            }

            $debt = Debt::create(array_merge($validator->validated(), [
                'user_id' => $user->id
            ]));

            return $this->success($debt, ApiSlug::DEBT_ADDED->value);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ApiSlug::DEBT_STORE_FAILED->value, 422);
        }
    }


    public function update($id, Request $request)
    {
        try {
            $user = auth()->user();

            $debt = Debt::where('id',$id)->where('user_id', $user->id)->first();

            if (!$debt) {
                return $this->error(
                    'Debt not found or you do not have permission to update it',
                    ApiSlug::PRAYER_REMOVE_FAILED->value,
                    404
                );
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'sometimes|integer',
                'due_date' => 'sometimes|date',
                'from' => 'string',
                'debt_type' => 'required|in:mardomi,banki',
                'bank_name' => 'string',
                'description' => 'string',
            ]);

            $debt->update($validator->validated());

            return $this->success($debt, ApiSlug::DEBT_UPDATED->value);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ApiSlug::DEBT_UPDATED_FAILED->value, 422);
        }
    }


    public function destroy($id)
    {
        try {
            $user = auth()->user();

            $debt = Debt::where('id',$id)->where('user_id', $user->id)->first();
            if (!$debt) {
                return $this->error(
                    'Debt not found or you do not have permission to delete it',
                    ApiSlug::PRAYER_REMOVE_FAILED->value,
                    404
                );
            }
            $debt->delete();

            return $this->success(null, ApiSlug::DEBT_REMOVED->value);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ApiSlug::DEBT_REMOVED_FAILED->value, 422);
        }
    }
}
