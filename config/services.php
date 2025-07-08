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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'timeout' => env('GEMINI_TIMEOUT', 30),
    ],

    'ghn' => [
        'token' => env('GHN_TOKEN', '316ea3e3-53fc-11f0-989d-42259a3f1d4c'),
        'base_url' => env('GHN_BASE_URL', 'https://online-gateway.ghn.vn/shiip/public-api'),
        'shop_id' => env('GHN_SHOP_ID', 4583816),
        'test_mode' => env('GHN_TEST_MODE', true),
        'auto_create_order' => env('GHN_AUTO_CREATE_ORDER', false),
        'from_province_id' => env('GHN_FROM_PROVINCE_ID', 202),
        'from_district_id' => env('GHN_FROM_DISTRICT_ID', 1542),
        'from_ward_code' => env('GHN_FROM_WARD_CODE', '20101'),
    ],

    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL'),
        'ipn_url' => env('VNPAY_IPN_URL'),
        'api_url' => env('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),
    ],


];
