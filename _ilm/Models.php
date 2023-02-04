<?php

namespace _ilmComm\Core;

use _ilmComm\DataBase;
use _ilmComm\UserAgent\Browser;

class Models
{
    /**
     * Database object
     *
     * @var DataBase
     */
    protected $DB;

    /**
     * Page model constructor
     */
    public function __construct()
    {
        $this->DB = new DataBase;
        $this->mobileView = Browser::getDeviceType(
            Browser::IS_MOBILE_VIEW
        );
    }
}
