<?php

return [
    'sms_ru' => [
        'api_id' => env('SMS_RU_API_ID'),
        'from' => env('SMS_RU_FROM', 'SMS'),
        'test' => env('SMS_RU_TEST', false), // Test mode
    ],
];
