<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Login protection (fail2ban-like)
    |--------------------------------------------------------------------------
    |
    | max_attempts: number of consecutive failed attempts allowed per
    | username + IP.
    | lockout_seconds: temporary ban duration once the threshold is reached.
    |
    */
    'login' => [
        'max_attempts' => env('SECURITY_LOGIN_MAX_ATTEMPTS', 5),
        'lockout_seconds' => env('SECURITY_LOGIN_LOCKOUT_SECONDS', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Global anti-DoS rate limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of requests per minute per client (authenticated user
    | when available, otherwise by IP).
    |
    */
    'dos' => [
        'max_requests_per_minute' => env('SECURITY_DOS_MAX_REQUESTS_PER_MINUTE', 240),
    ],
];
