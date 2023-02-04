<?php

namespace _ilmComm\UserAgent;

class Location
{
    /**
     * @param string $ip
     * @return mixed
     */
    public static function getLocation(string $ip)
    {
        $IpInfoUrl = "http://ipinfo.io/{$ip}/json";
        return json_decode(file_get_contents($IpInfoUrl));
    }
}
