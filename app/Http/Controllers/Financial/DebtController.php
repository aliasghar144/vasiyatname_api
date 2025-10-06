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

        $mardomi = Debt::where('user_id',$user->id)->where('type', 'mardomi')->get();
        $banki = Debt::where('user_id',$user->id)->where('type', 'banki')->get();
        $mehriye = Debt::where('user_id',$user->id)->where('type', 'mehriye')->get();

        return $this->success([
            'mardomi' => $mardomi,
            'banki' => $banki,
            'mehriye' => $mehriye,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'type' => 'required|in:mardomi,banki,mehriye',
                'amount' => 'required|integer',
                'amount_paid' => 'required|integer',
                'created_date' => 'required|date',
                'due_date' => 'required|date',
                'status' => 'required|string',
                'full_name' => 'required|string',
                'national_id' => 'nullable|string',
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
                'title' => 'sometimes|required|string',
                'type' => 'sometimes|required|in:mardomi,banki,mehriye',
                'amount' => 'sometimes|required|integer',
                'amount_paid' => 'sometimes|required|integer',
                'created_date' => 'sometimes|required|date',
                'due_date' => 'sometimes|required|date',
                'status' => 'sometimes|required|string',
                'full_name' => 'sometimes|required|string',
                'national_id' => 'nullable|string',
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
