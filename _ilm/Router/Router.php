<?php

namespace _ilmComm\Core\Router;

use _ilmComm\App\Controller\ErrorController;
use _ilmComm\Core\Http\Request\Request;
use _ilmComm\Core\Router\Traits\AjaxRouter;
use _ilmComm\Core\Router\Traits\AppEnvironment;
use _ilmComm\Core\Router\Traits\RouteWithHttpMethods;

class Router
{
    use AjaxRouter,
        AppEnvironment,
        RouteWithHttpMethods;

    /**
     * Request
     *
     * @var Request
     */
    private static $Rqst;

    /**
     * Is matched
     *
     * @var boolean
     */
    public static $IsMatched;

    /**
     * Router constructor
     *
     * @param Request $rq
     */
    public function __construct(Request $rq)
    {
        static::$Rqst = $rq;
        static::$IsMatched = false;
    }

    /**
     * Load user defined routes
     *
     * @param string $env
     * @return void
     */
    public function loadRoutes()
    {
        $UserRouteFiles = doc_root("doc/routes/{$this->AppEnv}.php");

        if (file_exists($UserRouteFiles)) {
            include $UserRouteFiles;
        }

        $this->cleanUp();
    }

    /**
     * Clean up router
     *
     * @return void
     */
    public function cleanUp()
    {
        if (!static::$IsMatched) {
            $i = ErrorController::getInstance();
            $i->setRequest(static::$Rqst);
            exit($i->init($this->AppEnv));
        }
    }
}
