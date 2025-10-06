<?php

namespace  App\Http\Controllers\Religious;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Prayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class  PrayersController extends  BaseController{

    public function index()
    {
        $user = auth()->user();

        $yomieh = Prayer::where('user_id',$user->id)->where('type', 'yomieh')->get();
        $ayat = Prayer::where('user_id',$user->id)->where('type', 'ayat')->get();
        $sajdeh = Prayer::where('user_id',$user->id)->where('type', 'sajdeh')->get();

        return $this->success([
            'yomieh' => $yomieh,
            'ayat'   => $ayat,
            'sajdeh' => $sajdeh,
        ]);
    }

    // ایجاد نماز
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'type'        => 'required|in:yomieh,ayat,sajdeh',
                'rakats'      => 'required|integer|min:1',
                'status'      => 'required|string',
                'date'        => 'required|date',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
            }

            $prayer = Prayer::create(array_merge($validator->validated(), [
                'user_id' => $user->id
            ]));

            return $this->success($prayer,ApiSlug::PRAYER_CREATED->value, 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ApiSlug::PRAYER_CREATED_FAILED->value, 422);
        }
    }

    // بروزرسانی نماز
    public function update($id, Request $request)
    {
        try {
            $user = auth()->user();

            $prayer = Prayer::where('id',$id)->where('user_id', $user->id)->first();

            if (!$prayer) {
                return $this->error(
                    'Prayer not found or you do not have permission to update it',
                    ApiSlug::PRAYER_REMOVE_FAILED->value,
                    404
                );
            }

            $validator = Validator::make($request->all(), [
                'type'        => 'sometimes|required|in:yomieh,ayat,sajdeh',
                'rakats'      => 'sometimes|required|integer|min:1',
                'status'      => 'sometimes|required|string',
                'date'        => 'sometimes|required|date',
                'description' => 'nullable|string',
            ]);

            $prayer->update($validator->validated());

            return $this->success($prayer, ApiSlug::PRAYER_UPDATED->value);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ApiSlug::PRAYER_UPDATE_FAILED->value, 422);
        }
    }

    // حذف نماز
    public function destroy($id)
    {
        try {
            $user = auth()->user();

            $prayer = Prayer::where('id',$id)->where('user_id', $user->id)->first();

            if (!$prayer) {
                return $this->error(
                    'Prayer not found or you do not have permission to delete it',
                    ApiSlug::PRAYER_REMOVE_FAILED->value,
                    404
                );
            }

            $prayer->delete();



            return $this->success(null, ApiSlug::PRAYER_REMOVED->value);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ApiSlug::PRAYER_REMOVE_FAILED->value, 422);
        }
    }
}
