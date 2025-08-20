<?php

return [

    'notify_url'       => env('PAYMENT_NOTIFY_URL', 'http://test.payment.com/notify'),
    'method'           => 'kbz.payment.precreate',
    'sign_type'        => 'SHA256',
    'version'          => '1.0',
    'merchant_code'    => env('PAYMENT_MERCHANT_CODE', '911004501'), // Short Code: (UAT)
    'appid'            => env('PAYMENT_APP_ID', 'kpe474a3a5101c7edb1bf8b84ffadb1b'), // App ID: (UAT)
    'trade_type'       => 'MINIAPP',
    'trans_currency'   => 'MMK',
    'timeout_express'  => '100m',
    'callback_info'    => 'KBZMINICallBack',
    'secret_key'       => env('PAYMENT_SECRET_KEY', '#N$w#%#Goen)qrH8zYM#MARqVtLEsqRc'), // App Key: (UAT)

];