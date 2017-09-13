<?php

namespace Dms\Web\Laravel\Tests\Integration\Pages;

use Dms\Web\Laravel\Tests\Integration\CmsIntegrationTest;
use Dms\Web\Laravel\Tests\Integration\Fixtures\Demo\DemoFixture;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IndexTest extends CmsIntegrationTest
{
    protected static function getFixture()
    {
        return new DemoFixture();
    }

    public function testUnauthenticatedIndexRedirectsToLoginPage()
    {
        $this->route('GET', 'dms::index');

        $this->assertRedirectedToRoute('dms::auth.login');
    }

    public function testAuthenticatedIndexPageShowsDashboard()
    {
        $this->actingAsUser();

        $this->route('GET', 'dms::index');

        $this->see('Dashboard');
    }
}