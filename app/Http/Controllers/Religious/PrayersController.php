<?php

namespace  App\Http\Controllers\Religious;

use App\Http\Controllers\BaseController;
use App\Models\Prayer;

class  PrayersController extends  BaseController{

    public function index()
    {
        $yomieh = Prayer::where('type', 'yomieh')->get();
        $ayat = Prayer::where('type', 'ayat')->get();
        $sajdeh = Prayer::where('type', 'sajdeh')->get();

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
            $validated = $request->validate([
                'type'        => 'required|in:yomieh,ayat,sajdeh',
                'rakats'      => 'required|integer|min:1',
                'status'      => 'required|string',
                'date'        => 'required|date',
                'description' => 'nullable|string',
            ]);

            $prayer = Prayer::create($validated);

            return $this->success($prayer, 'prayer_created', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'prayer_store_failed', 422);
        }
    }

    // بروزرسانی نماز
    public function update($id, Request $request)
    {
        try {
            $prayer = Prayer::findOrFail($id);

            $validated = $request->validate([
                'type'        => 'sometimes|required|in:yomieh,ayat,sajdeh',
                'rakats'      => 'sometimes|required|integer|min:1',
                'status'      => 'sometimes|required|string',
                'date'        => 'sometimes|required|date',
                'description' => 'nullable|string',
            ]);

            $prayer->update($validated);

            return $this->success($prayer, 'prayer_updated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'prayer_update_failed', 422);
        }
    }

    // حذف نماز
    public function destroy($id)
    {
        try {
            $prayer = Prayer::findOrFail($id);
            $prayer->delete();

            return $this->success(null, 'prayer_deleted');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'prayer_delete_failed', 422);
        }
    }
}
