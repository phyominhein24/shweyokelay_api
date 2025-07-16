<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class EncryptionHelper
{
    public static function generateSignature($params)
    {

        $signString = 
            'appid=' . $params['appid'] . '&' .
            'callback_info=' . $params['callback_info'] . '&' .
            'merch_code=' . $params['merch_code'] . '&' .
            'merch_order_id=' . $params['merch_order_id'] . '&' .
            'method=' . $params['method'] . '&' .
            'nonce_str=' . $params['nonce_str'] . '&' .
            'notify_url=' . $params['notify_url'] . '&' .
            'timeout_express=' . $params['timeout_express'] . '&' .
            'timestamp=' . $params['timestamp'] . '&' .
            'title=' . $params['title'] . '&' .
            'total_amount=' . $params['total_amount'] . '&' .
            'trade_type=' . $params['trade_type'] . '&' .
            'trans_currency=' . $params['trans_currency'] . '&' .
            'version=' . $params['version'] . '&' .
            'key=' . $params['key'];

        return Str::upper(hash(Config::get('payment.sign_type'), $signString));
    }

    public static function getSignForOrderInfo($params)
    {

        $signString = 
            'appid=' . $params['appid'] . '&' .
            'merch_code=' . $params['merch_code'] . '&' .
            'nonce_str=' . $params['nonce_str'] . '&' .
            'prepay_id=' . $params['prepay_id'] . '&' .
            'timestamp=' . $params['timestamp'] . '&' .
            'key=' . $params['key'];

        return Str::upper(hash(Config::get('payment.sign_type'), $signString));
    }

    public static function getSignForOrderInfoString($params)
    {

        $signString = 
            'appid=' . $params['appid'] . '&' .
            'merch_code=' . $params['merch_code'] . '&' .
            'nonce_str=' . $params['nonce_str'] . '&' .
            'prepay_id=' . $params['prepay_id'] . '&' .
            'timestamp=' . $params['timestamp'] . '&' .
            'key=' . $params['key'];

        return $signString;
    }

    public static function getSignForOrderInfo2($params)
    {

        $signString = 
            'access_token=' . $params['access_token'] . '&' .
            'appid=' . $params['appid'] . '&' .
            'merch_code=' . $params['merch_code'] . '&' .
            'method=' . $params['method'] . '&' .
            'nonce_str=' . $params['nonce_str'] . '&' .
            'resource_type=' . $params['resource_type'] . '&' .
            'timestamp=' . $params['timestamp'] . '&' .
            'trade_type=' . $params['trade_type'] . '&' .
            'version=' . $params['version'] . '&' .
            'key=' . $params['key'];

        return Str::upper(hash(Config::get('payment.sign_type'), $signString));
    }
}
