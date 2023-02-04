<?php

namespace _ilmComm\Core\Curl_SMS\Traits;

trait TemplateForCustomer
{
    public static function sendCustomerRegistrationOtpTemplate(int $otp_code)
    {
        return "Your " . COMPANY_NAME . " OTP code is " . $otp_code . ". \n" .
            "It will expire in 3 minutes.";
    }
}
