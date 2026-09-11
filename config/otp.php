<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Email OTP two-factor for staff login
    |--------------------------------------------------------------------------
    |
    | When enabled, every staff login is followed by a 6-digit code emailed to
    | the account, unless the browser holds a valid "trusted device" cookie.
    |
    */

    'enabled' => env('OTP_ENABLED', true),

    'ttl_minutes' => (int) env('OTP_TTL_MINUTES', 5),

    'trust_device_days' => (int) env('OTP_TRUST_DEVICE_DAYS', 30),

    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

];
