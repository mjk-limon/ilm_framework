<?php

namespace _ilmComm\Core\LocalStorage;

class Cookie
{
    /**
     * Set cookie
     *
     * @param string $key
     * @param string $value
     * @param integer $expire expires in second (time + $expiry)
     * @return boolean
     */
    public static function set(string $key, string $value, int $expire = 86400): bool
    {
        return setcookie($key, $value, time() + $expire, "/");
    }

    /**
     * Destroy cookie
     *
     * @param string $key
     * @return boolean
     */
    public static function destroy(string $key): bool
    {
        return setcookie($key, null, time() - 1, "/");
    }
}
