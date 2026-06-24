<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PawsNation Storefront Sync
    |--------------------------------------------------------------------------
    |
    | PetBarn is the source of truth for products, inventory and sales.
    | PawsNation (the customer storefront) mirrors this data and reports
    | online sales back. These settings secure the two-way link.
    |
    */

    // Bearer token PawsNation must present on every sync API request.
    'token' => env('SYNC_SHARED_TOKEN'),

    // Secret used to HMAC-sign inventory webhooks we push to PawsNation.
    'webhook_secret' => env('SYNC_WEBHOOK_SECRET'),

    'pawsnation' => [
        'webhook_url' => env('PAWSNATION_WEBHOOK_URL'),
        'timeout'     => (int) env('PAWSNATION_WEBHOOK_TIMEOUT', 8),
    ],
];
