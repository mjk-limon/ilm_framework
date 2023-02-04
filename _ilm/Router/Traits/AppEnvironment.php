<?php

namespace _ilmComm\Core\Router\Traits;

trait AppEnvironment
{
    /**
     * Application environment
     *
     * @var string
     */
    private $AppEnv = "";

    /**
     * Available environment chart with url prefix
     *
     * @var array
     */
    private $AvailableEnvChart = array(
        "/ajax" => "xml-http-main",
        "/graph/(.*)" => "graph",
        "/callbacks/(.*)" => "callback"
    );

    /**
     * Create environment
     *
     * @param string|null $env
     * @return void
     */
    public function createEnvironment(string $env = null): void
    {
        if (!empty($env)) {
            $this->AppEnv = $env;
            return;
        }
        
        $RequstPath = static::$Rqst->cleanPath()->getPath();

        foreach ($this->AvailableEnvChart as $UrlPrfix => $Environment) {
            if (preg_match("~^{$UrlPrfix}/?$~", $RequstPath)) {
                $this->AppEnv = $Environment;
                return;
            }
        }

        $this->AppEnv = "main";
    }
}