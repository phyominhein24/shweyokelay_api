<?php

namespace App\Utilities;

class GeneralHelper
{
    public static function getUnixTimestamp()
    {
        return time();
    }

    // public static function generateRandomString($length = 16)
    // {
    //     return bin2hex(random_bytes($length / 2));
    // }

    public static function generateRandomString($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}