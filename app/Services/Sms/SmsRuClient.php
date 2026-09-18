<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsRuClient
{
    private const ENDPOINT = 'https://sms.ru/sms/send';

    public function __construct(
        private string $apiId = '',
        private string $from = 'SMS',
        private bool $testMode = false,
    ) {}

    public function send(string $phone, string $message): bool
    {
        if ($this->testMode || app()->environment('testing')) {
            Log::channel('testing')->info('SMS (test mode)', [
                'phone' => $phone,
                'message' => $message,
            ]);
            return true;
        }

        try {
            $response = Http::timeout(10)->post(self::ENDPOINT, [
                'api_id' => $this->apiId,
                'to' => $this->formatPhone($phone),
                'msg' => $message,
                'from' => $this->from,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json(),
                ]);
                return true;
            } else {
                Log::warning('SMS sending failed', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS sending error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function formatPhone(string $phone): string
    {
        // SmsRu expects format: 79991234567 (without +)
        return ltrim($phone, '+');
    }
}
