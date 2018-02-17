<?php
/**
 * This is here until the code in helper.php is fixed
 */
namespace Dms\Web\Expressive;

use Dms\Ioc\IlluminateContainer;
use Illuminate\Container\Container;

class AppContainer
{
    private static $instance;

    private function __construct()
    {
    }

    public function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new IlluminateContainer(new Container());
        }

        return self::$instance;
    }
}
