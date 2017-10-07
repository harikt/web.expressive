<?php

namespace Dms\Web\Expressive\Tests\Integration\Pages\Auth;

use Dms\Web\Expressive\Tests\Integration\CmsIntegrationTest;
use Dms\Web\Expressive\Tests\Integration\Fixtures\Demo\DemoFixture;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LoginTest extends CmsIntegrationTest
{
    protected static function getFixture()
    {
        return new DemoFixture();
    }
    
    public function testLoginPageShowsForm()
    {
        $this->route('GET', 'dms::auth.login');

        $this->assertResponseOk();
    }

    public function testInvalidLoginAttempt()
    {
        $this->route('GET', 'dms::auth.login');
        $this->route('POST', 'dms::auth.login');

        $this->assertRedirectedToRoute('dms::auth.login');
        $this->assertSessionHasErrors(['username', 'password']);
    }

    public function testBeingLoggedAndGoingToLoginPageRedirectsToIndex()
    {
        $this->actingAsUser();

        $this->route('GET', 'dms::auth.login');
        $this->assertRedirectedToRoute('dms::index');
    }

    public function testInvalidCredentials()
    {
        $this->route('GET', 'dms::auth.login');
        $this->route('POST', 'dms::auth.login', [], [
                'username' => 'test',
                'password' => 'test',
        ]);

        $this->assertRedirectedToRoute('dms::auth.login');
        $this->assertSessionHasErrors(['username' => trans('dms::auth.failed')]);
    }

    public function testValidCredentialsLogsIn()
    {
        $this->route('POST', 'dms::auth.login', [], [
                'username' => 'admin',
                'password' => 'admin',
        ]);

        $this->assertRedirectedToRoute('dms::index');
    }
}
