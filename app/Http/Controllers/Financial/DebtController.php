<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\BaseController;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Debt;

class DebtController extends BaseController
{
    public function index()
    {
        $mardomi = Debt::where('type', 'mardomi')->get();
        $banki = Debt::where('type', 'banki')->get();
        $mehriye = Debt::where('type', 'mehriye')->get();

        return $this->success([
            'mardomi' => $mardomi,
            'banki' => $banki,
            'mehriye' => $mehriye,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
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

            $debt = Debt::create($validated);

            return $this->success($debt, 'debt_created', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'debt_store_failed', 422);
        }
    }


    public function update($id, Request $request)
    {
        try {
            $debt = Debt::findOrFail($id);

            $validated = $request->validate([
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

            $debt->update($validated);

            return $this->success($debt, 'debt_updated');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'debt_update_failed', 422);
        }
    }


    public function destroy($id)
    {
        try {
            $debt = Debt::findOrFail($id);
            $debt->delete();

            return $this->success(null, 'debt_deleted');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'debt_delete_failed', 422);
        }
    }
}
