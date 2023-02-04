<?php

namespace _ilmComm\Core\Router\Traits;


trait RouteWithHttpMethods
{
    /**
     * Get method routers
     *
     * @param string $pattern
     * @param callable $callback
     * @return void
     */
    public static function get(string $pattern, $callback)
    {
        if (
            static::$Rqst->getServerParam('REQUEST_METHOD') != "GET" &&
            !static::$Rqst->requestFromAjax()
        ) {
            return;
        }

        static::processWithUrl($pattern, $callback);
    }

    /**
     * Post method routers
     *
     * @param string $pattern
     * @param array|closure $callback
     * @return void
     */
    public static function post(string $pattern, $callback)
    {
        if (static::$Rqst->getServerParam('REQUEST_METHOD') != "POST") {
            return;
        }

        static::processWithUrl($pattern, $callback);
    }

    /**
     * All method router
     *
     * @param string $pattern
     * @param array|colsure $callback
     * @return void
     */
    public static function all(string $pattern, $callback)
    {
        static::processWithUrl($pattern, $callback);
    }

    private static function processWithUrl(string $pattern, $callback)
    {
        $url = (static::$Rqst)->cleanPath()->writeTiny();

        if (!static::$IsMatched && preg_match("~^{$pattern}$~", $url, $args)) {
            static::$IsMatched = true;

            if (is_array($callback)) {
                $ClassName = $callback[0];
                $MethodName = $callback[1];
                $FunctionArguments = array_splice($args, 1);

                $i = $ClassName::getInstance();
                $i->setRequest(static::$Rqst);
                exit($i->$MethodName(...$FunctionArguments));
            }

            call_user_func($callback);
        }
    }
}
