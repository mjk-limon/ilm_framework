<?php

namespace _ilmComm\Core\Router\Traits;

use _ilmComm\App\Controller\XmlHttpController;

trait AjaxRouter
{
    private static $IsMatchedAjax = false;

    /**
     * Ajax model group name
     *
     * @var string
     */
    private static $AjaxModelGroup;

    /**
     * Router ajax request
     *
     * @param string $key
     * @param array|string $model
     * @return void
     */
    public static function ajax(string $key, $model)
    {
        if (!static::$IsMatchedAjax && static::ajaxMatchCheck($key)) {
            static::$IsMatchedAjax = true;

            if (!is_array($model)) {
                $model = [static::$AjaxModelGroup, $model];
            }

            $i = new XmlHttpController();
            $i->setRequest(static::$Rqst);;
            exit($i->init($model));
        }
    }

    /**
     * Create ajax model group
     *
     * @param string $model
     * @param callable $func
     * @return void
     */
    public static function ajaxGroup(string $model, callable $func)
    {
        static::$AjaxModelGroup = $model;
        call_user_func($func);
        static::$AjaxModelGroup = null;
    }

    /**
     * Ajax post key match
     *
     * @param string $key
     * @return boolean
     */
    private static function ajaxMatchCheck(string $key): bool
    {
        $post_array = (static::$Rqst)->getRequestParams();
        return isset($post_array[$key]);
    }
}
