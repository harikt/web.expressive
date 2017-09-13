<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http;

class CurrentModuleContext
{
    protected static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }
}
