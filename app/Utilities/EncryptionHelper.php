<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class EncryptionHelper
{
    public static function generateSignature($params)
    {
        $signString = http_build_query($params)
            . '&key=' . Config::get('payment.secret_key');

        return Str::upper(hash(Config::get('payment.sign_type'), $signString));
    }
}