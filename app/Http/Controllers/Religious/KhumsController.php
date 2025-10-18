<?php

namespace  App\Http\Controllers\Religious;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Khums;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhumsController extends BaseController
{
    public function index()
    {
        $user = auth()->user();

        $khums = Khums::where('user_id', $user->id)->get(['id', 'date', 'amount']);

        return $this->success($khums);
    }

    public function details($id)
    {
        $user = auth()->user();
        $claim = Khums::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if (!$claim) {
            return $this->error('خمس یافت نشد', ApiSlug::KHUMS_NOTFOUND->value);
        }
        return $this->success($claim);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::KHUMS_ERROR->value, 400);
        }

        $claim = Khums::create(array_merge($validator->validated(), [
            'user_id' => $user->id
        ]));

        return $this->success($claim, ApiSlug::KHUMS_ADDED->value);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $khums = Khums::where('id', $id)->where('user_id', $user->id)->first();

        if (!$khums) {
            return $this->error(
                'khums not found or you do not have permission to update it',
                ApiSlug::KHUMS_UPDATE_FAILED->value,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|required|date',
            'amount' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'payed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $khums->update($validator->validated());

        return $this->success($khums, ApiSlug::KHUMS_UPDATED->value);
    }

    // حذف طلب
    public function destroy($id)
    {
        $user = auth()->user();
        $khums = khums::where('id', $id)->where('user_id', $user->id)->first();
        if (!$khums) {
            return $this->error(
                'Khums not found or you do not have permission to delete it',
                ApiSlug::KHUMS_REMOVED_FAILED->value,
                404
            );
        }
        $khums->delete();

        return $this->success(null, ApiSlug::KHUMS_REMOVED->value,);
    }

}
