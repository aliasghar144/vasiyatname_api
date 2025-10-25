<?php

namespace App\Services;

use GuzzleHttp\Client;

class FcmService
{
    protected $http;
    protected $accessToken;

    public function __construct()
    {
        $this->http = new Client();
//        $path = storage_path(env('FIREBASE_CREDENTIALS'));
//        $credentials = json_decode(file_get_contents($path), true);
        $credentials = json_decode(file_get_contents(env('FIREBASE_CREDENTIALS')), true);

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $creds = new \Google\Auth\Credentials\ServiceAccountCredentials($scopes, $credentials);
        $this->accessToken = $creds->fetchAuthToken()['access_token'];
        $this->projectId = $credentials['project_id'];
    }

    public function send($fcmToken, $title, $body, array $data = [])
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        if (!is_array($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }

        // حتماً data Map باشه، اگر خالیه، یه آرایه خالی ارسال کن
        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data ?? [],
            ],
        ];

        $response = $this->http->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type'  => 'application/json',
            ],
            'json' => $payload,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function sendToUser(int $userId, string $title, string $body, array $data = [])
    {
        $tokens = \App\Models\User::where('user_id', $userId)->pluck('fcm_token')->toArray();

        foreach ($tokens as $token) {
            $this->send($token, $title, $body, $data);
        }
    }

}
