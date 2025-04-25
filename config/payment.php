<?php

return [

    'notify_url'       => env('PAYMENT_NOTIFY_URL', 'https://yourdomain.com/payment/notify'),
    'method'           => 'pay.createOrder',
    'sign_type'        => 'sha256',
    'version'          => '1.0',
    'merchant_code'    => env('PAYMENT_MERCHANT_CODE', '911004501'),
    'appid'            => env('PAYMENT_APP_ID', 'kpe474a3a5101c7edb1bf8b84ffadb1b'),
    'trade_type'       => 'APP',
    'trans_currency'   => 'MMK',
    'timeout_express'  => '15m',
    'callback_info'    => 'miniapp_callback',
    'secret_key'       => env('PAYMENT_SECRET_KEY', 'your_secret_key'),

];