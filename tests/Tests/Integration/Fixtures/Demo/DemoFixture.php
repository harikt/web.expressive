<?php

namespace Dms\Web\Laravel\Tests\Integration\Fixtures\Demo;

use Dms\Web\Laravel\Tests\Integration\Fixtures\DmsFixture;
use Illuminate\Contracts\Foundation\Application;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DemoFixture extends DmsFixture
{
    /**
     * @return string
     */
    public function getCmsClass()
    {
        return DemoCms::class;
    }

    /**
     * @return string
     */
    public function getOrmClass()
    {
        return DemoOrm::class;
    }

    protected function seed(Application $app)
    {
        $this->runSeeder($app, DemoDatabaseSeeder::class);
    }
}