<?php

namespace _ilmComm\UserAgent;

class PublicIp
{
    /**
     * Get public ip
     *
     * @return string
     */
    public static function getIp(): string
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return "127.0.0.1";
        }
    }
}
