<?php

namespace Dms\Web\Laravel\Tests\Unit;

use Dms\Common\Testing\DmsAsserts;
use Dms\Web\Laravel\DmsServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class UnitTest extends TestCase
{
    use DmsAsserts;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [DmsServiceProvider::class];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
        $app['config']->set('dms', require __DIR__ . '/../../../config/dms.php');
    }
}