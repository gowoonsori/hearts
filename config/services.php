<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'saramin' => [
        'client_id' => env('SARAMIN_ID'),
        'client_secret' => env('SARAMIN_SECRET'),
        'redirect' => env('SARAMIN_CALLBACK'),
    ],

    'naver' => [
        'client_id' => env('NAVER_ID'),
        'client_secret' => env('NAVER_SECRET'),
        'redirect' => env('NAVER_CALLBACK'),
    ]

];
