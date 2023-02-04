<?php

namespace _ilmComm\Core\Curl_SMS;

use _ilmComm\Core\Curl_SMS\Traits\TemplateForCustomer;
use _ilmComm\Core\Curl_SMS\Traits\TemplateForOrder;

class SmsTemplates
{
    use TemplateForOrder;
    use TemplateForCustomer;

    public static function create(string $page_name, array $args = []): string
    {
        $mthd = $page_name . "Template";

        if (method_exists(static::class, $mthd)) {
            return call_user_func([static::class, $mthd], ...$args);
        }

        return "";
    }
}
