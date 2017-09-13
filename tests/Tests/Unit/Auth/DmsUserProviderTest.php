<?php

namespace Dms\Web\Laravel\Tests\Unit\Auth;

use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Tests\Persistence\Db\Integration\Mapping\DbIntegrationTest;
use Dms\Web\Laravel\Auth\Admin;
use Dms\Web\Laravel\Auth\AdminDmsUserProvider;
use Dms\Web\Laravel\Auth\Password\BcryptPasswordHasher;
use Dms\Web\Laravel\Auth\Password\PasswordHasherFactory;
use Dms\Web\Laravel\Auth\Persistence\AdminRepository;
use Dms\Web\Laravel\Auth\Persistence\AuthOrm;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsUserProviderTest extends DbIntegrationTest
{
    /**
     * @var AdminDmsUserProvider
     */
    protected $userProvider;

    /**
     * @var AdminRepository
     */
    protected $userRepo;

    /**
     * @return IOrm
     */
    protected function loadOrm()
    {
        return new AuthOrm();
    }

    public function setUp()
    {
        parent::setUp();

        $hasher = new PasswordHasherFactory([
            'bcrypt' => function (int $cost) {
                return new BcryptPasswordHasher($cost);
            },
        ], 'bcrypt', 10);

        $this->userRepo     = new AdminRepository($this->connection, $this->orm);
        $this->userProvider = new AdminDmsUserProvider($this->userRepo, $hasher);

        $this->setDataInDb([
            'users' => [
                [
                    'id'                   => 1,
                    'type'                 => 'local',
                    'full_name'            => 'Admin',
                    'email'                => 'admin@admin.com',
                    'username'             => 'admin',
                    'password_hash'        => $hasher->buildDefault()->hash('password')->getHash(),
                    'password_algorithm'   => 'bcrypt',
                    'password_cost_factor' => 10,
                    'is_super_user'        => true,
                    'is_banned'            => false,
                    'remember_token'       => 'some_token',
                    'oauth_account_id'     => null,
                    'oauth_provider_name'  => null,
                ],
                [
                    'id'                   => 2,
                    'type'                 => 'local',
                    'full_name'            => 'User',
                    'email'                => 'user@user.com',
                    'username'             => 'user',
                    'password_hash'        => $hasher->buildDefault()->hash('password1')->getHash(),
                    'password_algorithm'   => 'bcrypt',
                    'password_cost_factor' => 10,
                    'is_super_user'        => false,
                    'is_banned'            => false,
                    'remember_token'       => null,
                    'oauth_account_id'     => null,
                    'oauth_provider_name'  => null,
                ],
            ],
        ]);
    }

    public function testRetrieveById()
    {
        /** @var Admin $user */
        $user = $this->userProvider->retrieveById(1);

        $this->assertInstanceOf(Admin::class, $user);
        $this->assertSame(1, $user->getId());

        /** @var Admin $user */
        $user = $this->userProvider->retrieveById(2);

        $this->assertInstanceOf(Admin::class, $user);
        $this->assertSame(2, $user->getId());

        $this->assertSame(null, $this->userProvider->retrieveById('non_existent_username'));
    }

    public function testRetrieveByCredentials()
    {
        /** @var Admin $user */
        $user = $this->userProvider->retrieveByCredentials([
            'username' => 'admin',
            'password' => 'does_not_matter',
        ]);

        $this->assertInstanceOf(Admin::class, $user);
        $this->assertSame(1, $user->getId());

        /** @var Admin $user */
        $user = $this->userProvider->retrieveByCredentials([
            'username' => 'user',
            'password' => 'does_not_matter',
        ]);

        $this->assertInstanceOf(Admin::class, $user);
        $this->assertSame(2, $user->getId());

        $this->assertSame(null, $this->userProvider->retrieveByCredentials([
            'username' => 'non_existent_username',
            'password' => 'does_not_matter',
        ]));
    }

    public function testUpdateRememberToken()
    {
        /** @var Admin $user */
        $user = $this->userRepo->get(1);
        $this->userProvider->updateRememberToken($user, 'new_token');

        $this->assertSame('new_token', $user->getRememberToken());
        $this->assertSame('new_token', $this->db->getTable('users')->getRows()[1]['remember_token']);
    }

    public function testRetrieveByToken()
    {
        /** @var Admin $user */
        $user = $this->userProvider->retrieveByToken(1, 'some_token');

        $this->assertInstanceOf(Admin::class, $user);
        $this->assertSame(1, $user->getId());

        $this->assertSame(null, $this->userProvider->retrieveByToken(1, 'non_existent_token'));
    }

    public function testValidateCredentials()
    {
        /** @var Admin $user */
        $user = $this->userRepo->get(1);

        $this->assertSame(true, $this->userProvider->validateCredentials($user, ['password' => 'password']));
        $this->assertSame(false, $this->userProvider->validateCredentials($user, ['password' => 'password1']));
        $this->assertSame(false, $this->userProvider->validateCredentials($user, ['password' => 'abc']));
    }
}