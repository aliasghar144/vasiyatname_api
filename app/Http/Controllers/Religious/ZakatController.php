<?php

namespace  App\Http\Controllers\Religious;

use App\Enums\ApiSlug;
use App\Http\Controllers\BaseController;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZakatController extends BaseController
{

    public function index()
    {
        $user = auth()->user();

        $mali = Zakat::where('user_id',$user->id)->where('type', 'mali')->get(['id', 'amount', 'description' , 'date']);
        $fetri = Zakat::where('user_id',$user->id)->where('type', 'fetri')->get(['id', 'amount', 'description', 'date']);

        if (!$mali || !$fetri) {
            return $this->error('طلب یافت نشد', ApiSlug::ZAKAT_NOTFOUND->value);
        }
        return $this->success([
            'mali' => $mali,
            'fetri' => $fetri,
        ]);
    }

    public function details($id)
    {
        $user = auth()->user();
        $zakat = Zakat::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if (!$zakat) {
            return $this->error('زکات یافت نشد', ApiSlug::ZAKAT_NOTFOUND->value);
        }
        return $this->success($zakat);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'amount' => 'required|string',
            'type' => 'in:mali,fetri',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::ZAKAT_ERROR->value, 400);
        }

        $zakat = Zakat::create(array_merge($validator->validated(), [
            'user_id' => $user->id
        ]));

        return $this->success($zakat, ApiSlug::ZAKAT_ADDED->value);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $zakat = Zakat::where('id', $id)->where('user_id', $user->id)->first();

        if (!$zakat) {
            return $this->error(
                'khums not found or you do not have permission to update it',
                ApiSlug::ZAKAT_UPDATE_FAILED->value,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'date' => 'sometimes|required|date',
            'amount' => 'sometimes|required|string',
            'type' => 'in:fetri,mali',
            'description' => 'nullable|string',
            'payed' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), ApiSlug::VALIDATION_ERROR->value, 400);
        }

        $zakat->update($validator->validated());

        return $this->success($zakat, ApiSlug::ZAKAT_UPDATED->value);
    }

    // حذف طلب
    public function destroy($id)
    {
        $user = auth()->user();
        $zakat = Zakat::where('id', $id)->where('user_id', $user->id)->first();
        if (!$zakat) {
            return $this->error(
                'Khums not found or you do not have permission to delete it',
                ApiSlug::ZAKAT_REMOVED_FAILED->value,
                404
            );
        }
        $zakat->delete();

        return $this->success(null, ApiSlug::ZAKAT_REMOVED->value,);
    }

}
