<?php

namespace _ilmComm\Core\Curl_SMS;

use _ilmComm\Exceptions\IlmExceptions;

class Sms
{
    /**
     * Sms to
     *
     * @var string
     */
    protected $SmsTo = "";

    /**
     * Sms gateway endpoint
     *
     * @var string
     */
    protected $EndPoint = "";

    /**
     * Request type is post
     *
     * @var boolean
     */
    protected $ReqTypeIsPost = false;

    /**
     * Request params
     *
     * @var array
     */
    protected $RequestParams = array();

    /**
     * Gateway response type
     *
     * @var string
     */
    protected $ResponseType = "json";

    /**
     * Response success key
     *
     * @var string
     */
    protected $SuccessKey = "";

    /**
     * Sms constructor
     */
    public function __construct()
    {
        $Settings = json_decode(get_site_settings('smsao'), true);

        if (!empty($Settings)) {
            $this->ReqTypeIsPost = rec_arr_val($Settings, "rt") != 1;
            $this->EndPoint      = rec_arr_val($Settings, "u");
            $this->RequestParams = rec_arr_val($Settings, "p");
            $this->ResponseType  = rec_arr_val($Settings, "ret", "json");
            $this->SuccessKey    = rec_arr_val($Settings, "sk");
        }
    }

    /**
     * Set sms receiver
     *
     * @param string $eT
     * @return void
     */
    public function setSmsReceiver(string $eT)
    {
        $this->SmsTo = trim($eT);
    }

    /**
     * Send message
     *
     * @param string $sms_text
     * @return void
     */
    public function sendMessage(string $sms_text)
    {
        if (adm_fet('_ilm_opt', 'smsao') && $this->EndPoint) {
            $this->processMessageText($sms_text);

            // Init request
            $req = curl_init();
            curl_setopt($req, CURLOPT_URL, $this->EndPoint);
            curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);

            if ($this->ReqTypeIsPost) {
                // If post method, init request post fields
                curl_setopt($req, CURLOPT_POST, true);
                curl_setopt($req, CURLOPT_POSTFIELDS, $this->RequestParams);
            }

            // Execute and close
            $Result = curl_exec($req);
            curl_close($req);

            // Check success response
            if (!$this->checkResponse($Result)) {
                $ErrorMsg = "Message not sent! Please try again... <br/>";
                throw new IlmExceptions($ErrorMsg);
            }

            return $Result;
        }

        throw new IlmExceptions("Service not available !");
    }

    private function processMessageText(string $sms_text)
    {
        // Assign sms text and receiver values from variable
        $this->RequestParams = array_map(function ($v) use ($sms_text) {
            $v = preg_replace("/{{sms_to}}/i", $this->SmsTo, $v);
            $v = preg_replace("/{{sms_text}}/i", $sms_text, $v);
            return $v;
        }, $this->RequestParams);

        // If get request, build query parameter
        if (!$this->ReqTypeIsPost) {
            $this->EndPoint .= "?" . http_build_query($this->RequestParams);
        }
    }

    /**
     * Check response success or false
     *
     * @param mixed $result
     * @return boolean
     */
    private function checkResponse($result): bool
    {
        if (!$this->SuccessKey) {
            // If success key not set, return success
            return true;
        }

        if ($this->ResponseType == 'json') {
            // If response type json, decode it
            $Response = json_decode($result, true);
            return (bool) rec_arr_val($Response, $this->SuccessKey);
        }

        // check success key matched to result
        return $result == $this->SuccessKey;
    }
}
