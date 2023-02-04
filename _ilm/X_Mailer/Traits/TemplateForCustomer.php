<?php

namespace _ilmComm\Core\X_Mailer\Traits;

trait TemplateForCustomer
{
    protected static function welcomeMailCustomerTemplate(string $lastname)
    {
        return static::baseTemplate(
            "Greatings from " . COMPANY_NAME,
            "<h4>Hi {$lastname},</h4>
<p>Welcome! You're now part of a community of " . COMPANY_NAME . " family. Glad to see u.</p>
<p>
    <u>Lets Get Started:</u><br/>
    <ul>
        <li>Keep browsing products from our website</li>
        <li>Add your favorite product to cart</li>
        <li>Request a product order from us with your account information</li>
        <li>Pay us through our available payment methods</li>
        <li>Keep your account active. And do regular security checks</li>
        <li>Contact our support center for any assistance</li>
    </ul>
</p>
Happy shopping<br/>
" . COMPANY_NAME . " team."
        );
    }
}
