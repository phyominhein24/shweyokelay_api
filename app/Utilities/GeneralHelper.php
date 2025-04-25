<?php

namespace App\Utilities;

class GeneralHelper
{
    public static function getUnixTimestamp()
    {
        return time();
    }

    public static function generateRandomString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }
}