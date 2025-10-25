<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FcmController extends Controller
{
//    protected $fcm;

//    public function __construct(FcmService $fcm)
//    {
//        $this->fcm = $fcm;
//    }

    public function sendTest(Request $request)
    {
        $fcm = new FcmService();

        $validator = Validator::make($request->all(), [
            'fcmToken' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
//            'data' => 'map'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $data = $request->input('data', []);

            if (is_string($data)) {
                $decoded = json_decode($data, true);
                $data = is_array($decoded) ? $decoded : [];
            }

            $result = $fcm->send(
                $validated['fcmToken'],
                $validated['title'],
                $validated['body'],
                $data
            );

            Notifications::create([
                'user_id' => 2,
                'title' => $validated['title'],
                'body'  => $validated['body'],
                'data'  => $data,
                'read'  => false,
            ]);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
