<?php

namespace _ilmComm\Core\Http\Request;

class Request extends Url
{
    /**
     * Request parameters
     *
     * @var array
     */
    protected $RequestParams = array();

    /**
     * Request from ajax
     *
     * @var boolean
     */
    protected $RequestFromAjax = false;

    /**
     * Request constructor
     */
    public function __construct()
    {
        // Collect data from server variable
        $RequestUrl = $this->getServerParam('REQUEST_URI');

        if ($this->getServerParam('REQUEST_METHOD') == 'POST') {
            // Request method is post
            // Bind post params as request params
            $this->RequestParams = $_POST;
            
            if (strtolower($this->getServerParam('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
                // If xml-http (ajax) request
                $this->RequestFromAjax = true;

                if ($AjaxRequestPage = rec_arr_val($this->RequestParams, "page")) {
                    $RequestUrl = PROJECT_FOLDER . ltrim($AjaxRequestPage, "/");
                }
            }
        } else {
            // Request method is get
            // Bind get params as request params
            $this->RequestParams = $_GET;
        }

        parent::__construct($RequestUrl);
    }

    /**
     * Request is from ajax
     *
     * @return boolean
     */
    public function requestFromAjax(): bool
    {
        return $this->RequestFromAjax;
    }

    /**
     * Get request parameter
     *
     * @var string $k
     * @return mixed
     */
    public function getRequestParam(string $k)
    {
        return rec_arr_val($this->RequestParams, $k);
    }

    /**
     * Get server parameter
     *
     * @param string $k
     * @return mixed
     */
    public function getServerParam(string $k)
    {
        return rec_arr_val($_SERVER, $k);
    }

    /**
     * Get request parameters
     *
     * @return array
     */
    public function getRequestParams(): array
    {
        return $this->RequestParams;
    }
}
