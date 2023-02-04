<?php

namespace _ilmComm\Core\X_Mailer;

use _ilmComm\Core\X_Mailer\Traits\TemplateDefault;
use _ilmComm\Core\X_Mailer\Traits\TemplateForCustomer;

class MailTemplate
{
    use TemplateDefault,
        TemplateForCustomer;

    public static function create(string $page_name, array $args = []): string
    {
        $mthd = $page_name . "Template";

        if (method_exists(static::class, $mthd)) {
            return call_user_func([static::class, $mthd], ...$args);
        }

        return "";
    }

    protected static function baseTemplate(string $title, string $message_body): string
    {
        $Year = date("y");
        $BaseUrl = base_url();
        $MailTitle = htmlspecialchars($title);
        $MailBody = self::cleanMailBody($message_body);
        $CompanyName = COMPANY_NAME;
        $CompanyLogo = get_logo();
        $CompanyNumber = get_contact_information("mobile1");
        $CompanyAddress = get_contact_information("address");

        $TemplateBody = <<<HTMLELEMENTS
<div style="font-family:Arial;width:100%;display:block">
    <div style="border:1px solid #ddd;max-width:650px;margin:2em auto;overflow:hidden">
        <div style="overflow:hidden;border-bottom:1px solid #ddd">
            <table border="0" width="100%" cellpadding="0" cellspacing="0" height="40" style="position:relative;padding:1em">
                <tr>
                    <td>
                        <div style="display:block;width:100%">
                            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                                <tr><td style="text-align:center"><a href="{$BaseUrl}"><img src="{$CompanyLogo}" style="height: 80px;position:relative;z-index:2"/></a></td></tr>
                                <tr><td colspan="3" height="30"></td></tr>
                                <tr>
                                    <td colspan="3" style="text-align:center;font-family:Eras ITC,serif">
                                        <h2 style="color:#000;font-size:24px;text-shadow:0px 1px 2px #ddd;position:relative;z-index:2">{$MailTitle}</h2>
                                    </td>
                                </tr>
                                <tr><td colspan="3" height="20"></td></tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr><td height="10"></td></tr>
            <tr><td style="padding: 1em">{$MailBody}</td></tr>
            <tr><td height="10"></td></tr>
        </table>
        <table border="0" width="100%" cellpadding="0" cellspacing="0" style="padding: 2em 0">
            <tr><td style="text-align:center;font-size:12px;color:#111">This email is intended for you</td></tr>
            <tr><td style="text-align:center;font-size:12px;color:#111">From, {$CompanyName}</td></tr>
            <tr>
                <td style="text-align:center;font-size:12px;color:#111">{$CompanyAddress}. Call: {$CompanyNumber}</td>
            </tr>
            <tr>
                <td style="text-align:center;font-size:12px;color:#111">For more information please visit us at {$BaseUrl}</td>
            </tr>
            <tr>
                <td style="text-align:center;font-size:12px;color:#111">©{$Year} {$CompanyName}, All Rights Reserved.</td>
            </tr>
        </table>
    </div>
</div>
HTMLELEMENTS;

        return preg_replace(['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'], ['>', '<', '\\1'], $TemplateBody);
    }

    /**
     * Clean mail body
     *
     * @param string $body
     * @return string
     */
    protected static function cleanMailBody(string $body): string
    {
        $bad = array("content-type", "bcc:", "to:", "cc:");
        return str_replace($bad, "", $body);
    }
}
