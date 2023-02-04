<?php

namespace _ilmComm\Core\LocalStorage;

class Session
{
    const SESSIONVALUE_MERGE = 0;
    const SESSIONVALUE_OVERWRITE = 1;

    /**
     * Session initialize
     *
     * @return void
     */
    public static function init()
    {
        @session_start();
    }

    /**
     * Set sesion key
     *
     * @param array|string $key
     * @param array|string $value
     * @param integer $Ct 1 - Overwrite, 2 - Merge
     * @return void
     */
    public static function set($key, $value, $Ct = self::SESSIONVALUE_OVERWRITE)
    {
        if (is_array($key)) {
            return self::recursiveKeyOperation($key, $_SESSION, function (&$Arr, $k) use ($value, $Ct) {
                if ($Ct == self::SESSIONVALUE_MERGE) {
                    if (is_array($Arr[$k])) {
                        $Arr[$k][] = $value;
                    } else {
                        $Arr[$k] = [$Arr[$k], $value];
                    }
                } else {
                    $Arr[$k] = $value;
                }
            });
        }

        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     *
     * @param string|array $key
     * @return mixed
     */
    public static function get($key)
    {
        if (is_array($key)) {
            $Output = null;
            self::recursiveKeyOperation($key, $_SESSION, function ($Arr, $k) use (&$Output) {
                $Output = $Arr[$k];
            });
            return $Output;
        }

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;
    }

    /**
     * Destroy session
     *
     * @param null|string|array $key
     * @return void
     */
    public static function destroy($key = null)
    {
        if ($key) {
            if (is_array($key)) {
                self::recursiveKeyOperation($key, $_SESSION, function (&$Arr, $k) {
                    if (is_int($k)) {
                        array_splice($Arr, $k, 1);
                    } else {
                        unset($Arr[$k]);
                    }
                });
            } else {
                unset($_SESSION[$key]);
            }
        } else {
            session_destroy();
        }
    }

    /**
     * Recursive key operation
     *
     * @param array $Keys
     * @param array $Arr
     * @param callable $Func
     * @return mixed
     */
    private static function recursiveKeyOperation(array $Keys, array &$Arr, callable $Func)
    {
        foreach ($Keys as $k => $v) {
            if (!isset($Arr[$k])) {
                $Arr[$k] = [];
            }

            if ($v === []) {
                if (isset($Arr[$k])) {
                    return $Func($Arr, $k);
                }
            } else {
                if (!is_array($Arr[$k])) {
                    $Arr[$k] = [];
                }

                self::recursiveKeyOperation($v, $Arr[$k], $Func);
            }
        }
    }
}
