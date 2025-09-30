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
        $debts = Claim::where('user_id', $user->id)->get();

        return $this->success($debts);
    }

    // ایجاد طلب جدید
    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'from' => 'required|string|max:255',
            'relation' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'subject' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'check_number' => 'nullable|string|max:50',
            'status' => 'in:pending,received',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $debt = Claim::create(array_merge($validator->validated(), [
            'user_id' => $user->id
        ]));

        return $this->success($debt, ApiSlug::DEBT_ADDED->value);
    }

    // بروزرسانی طلب
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $debt = Claim::where('user_id', $user->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'from' => 'sometimes|required|string|max:255',
            'relation' => 'sometimes|required|string|max:255',
            'due_date' => 'nullable|date',
            'subject' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'check_number' => 'nullable|string|max:50',
            'status' => 'in:pending,received',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $debt->update($validator->validated());

        return $this->success($debt, ApiSlug::DEBT_UPDATED->value);
    }

    // حذف طلب
    public function destroy($id)
    {
        $user = auth()->user();
        $debt = Claim::where('user_id', $user->id)->findOrFail($id);

        $debt->delete();

        return $this->success(null, ApiSlug::DEBT_DELETED->value,);
    }


}
