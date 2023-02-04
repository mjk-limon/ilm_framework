<?php

namespace _ilmComm\Core\Curl_SMS\Traits;

trait TemplateForOrder
{
    public static function customerOrderTemplate(string $inv_no): string
    {
        return "Successfully placed your Order \"" . $inv_no . "\". \n" .
            COMPANY_NAME . ". Helpline: " .
            get_contact_information("mobile1");
    }

    public static function adminOrderTemplate(string $inv_no, string $mobile_number): string
    {
        return "New order received from your website. Order no: \"" . $inv_no . "\". " .
            "Customer number: " . $mobile_number;
    }
}
