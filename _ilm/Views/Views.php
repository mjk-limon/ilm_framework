<?php

namespace _ilmComm\Core\Views;

use _ilmComm\Core\Views\Traits\Layout;
use _ilmComm\Core\Views\Traits\Render;
use _ilmComm\Head\InitHead\MetaData\MetaData;

class Views
{
    use Render,
        Layout;

    const LAYOUT_HTML = 1;
    const LAYOUT_ASSETS_CSS = 2;
    const LAYOUT_ASSETS_JAVASCRIPT = 3;

    /**
     * Head meta data
     *
     * @var object
     */
    private $HeadMeta;

    /**
     * @param object $meta
     * @return boolean|void
     */
    public function setHeadMetaData($meta)
    {
        if (!($meta instanceof MetaData)) {
            return false;
        }

        $this->HeadMeta = $meta;
    }

    /**
     * Unknown function call handler
     *
     * @param string $name
     * @param mixed $arguments
     * @return void
     */
    public function __call(string $name, $arguments)
    {
        $IndexModel = $this->PageModel;
        return $IndexModel->{$name}(...$arguments);
    }

    /**
     * Extra model
     *
     * @param string $model
     * @return object
     */
    public function extModel(string $model): object
    {
        $model = "_ilmComm\\App\\Models\\" . $model;
        return new $model;
    }
}
