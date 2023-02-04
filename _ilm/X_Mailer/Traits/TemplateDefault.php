<?php

namespace _ilmComm\Core\X_Mailer\Traits;

trait TemplateDefault
{
    protected static function defaultTemplate(string $title, string $message)
    {
        return static::baseTemplate($title, $message);
    }
}
