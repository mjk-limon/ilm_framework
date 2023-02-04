<?php

namespace _ilmComm\Core\Http\Controller;

use _ilmComm\App\Models\MyAppModels;
use _ilmComm\Core\Http\MiddleWare\MiddleWare;
use _ilmComm\Core\Http\Request\Request;
use _ilmComm\Core\Http\UserAgent\Browser;
use _ilmComm\Core\Models;
use _ilmComm\Core\Views\Views;
use _ilmComm\Head\InitHead\MetaData\DefaultPageMeta;
use _ilmComm\Head\InitHead\MetaData\MetaData;

abstract class Controller
{
    /**
     * Request object
     *
     * @var Request
     */
    protected $Rqst;

    /**
     * Target info
     *
     * @var string|array
     */
    protected $Target = array();

    /**
     * Model object
     *
     * @var object
     */
    protected $Model;

    /**
     * View object
     *
     * @var Views
     */
    protected $Views;

    /**
     * Metadata object
     *
     * @var object
     */
    protected $MetaData;

    /**
     * Instance object
     *
     * @var $this
     */
    protected static $Instance;

    /**
     * @return $this
     */
    public static function getInstance()
    {
        $className = static::class;
        self::$Instance = new $className;
        return self::$Instance;
    }

    /**
     * @param Request $rq
     * @return void
     */
    public function setRequest(Request $rq)
    {
        $this->Rqst = $rq;
    }

    /**
     * @param string|array $t
     * @return void
     */
    protected function setTarget($t)
    {
        $this->Target = $t;
    }

    /**
     * Prepare controller object
     *
     * @return void
     */
    protected function prepare()
    {
        foreach (func_get_args() as $obj_path) {
            if (!class_exists($obj_path)) {
                continue;
            }

            $new_obj = new $obj_path;

            if ($new_obj instanceof Models) {
                $this->Model = $new_obj;
            } elseif ($new_obj instanceof MiddleWare) {
                // $this->MetaData = $new_obj;
            } elseif ($new_obj instanceof MetaData) {
                $this->MetaData = $new_obj;
            }
        }

        if (!$this->Views) {
            $this->Views = new Views;
            $this->Views->Language = SITE_LANGUAGE;
            $this->Views->mobileView = Browser::getDeviceType(Browser::IS_MOBILE_VIEW);
        }

        if (!$this->Model) {
            $this->Model = new MyAppModels;
        }

        if (!$this->MetaData) {
            $this->MetaData = new DefaultPageMeta;
        }
    }

    /**
     * Process render
     *
     * @return void
     */
    protected function processRender()
    {
        $this->Views->render($this->Target);
    }
}
