<?php

namespace Dms\Web\Expressive\Tests\Unit\Auth\Persistence;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Auth\Permission;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Tests\Persistence\Db\Integration\Mapping\DbIntegrationTest;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\LocalAdmin;
use Dms\Web\Expressive\Auth\OauthAdmin;
use Dms\Web\Expressive\Auth\Password\HashedPassword;
use Dms\Web\Expressive\Auth\Password\PasswordResetToken;
use Dms\Web\Expressive\Auth\Persistence\AdminRepository;
use Dms\Web\Expressive\Auth\Persistence\AuthOrm;
use Dms\Web\Expressive\Auth\Persistence\PasswordResetTokenRepository;
use Dms\Web\Expressive\Auth\Persistence\RoleRepository;
use Dms\Web\Expressive\Auth\Role;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AuthOrmTest extends DbIntegrationTest
{
    /**
     * @var AdminRepository
     */
    protected $userRepo;

    /**
     * @var RoleRepository
     */
    protected $roleRepo;

    /**
     * @var PasswordResetTokenRepository
     */
    protected $passwordResetTokenRepo;

    /**
     * @return IOrm
     */
    protected function loadOrm()
    {
        return new AuthOrm();
    }

    /**
     * @inheritDoc
     */
    protected function mapperAndRepoType()
    {
        return Admin::class;
    }

    public function setUp()
    {
        parent::setUp();

        $this->userRepo               = new AdminRepository($this->connection, $this->orm);
        $this->roleRepo               = new RoleRepository($this->connection, $this->orm);
        $this->passwordResetTokenRepo = new PasswordResetTokenRepository($this->connection, $this->orm);
    }

    public function testSaveLocalAdmin()
    {
        $this->userRepo->save(new LocalAdmin(
            'Admin',
            new EmailAddress('admin@admin.com'),
            'admin',
            new HashedPassword('hash', 'bcrypt', 10)
        ));

        $this->assertDatabaseDataSameAs([
            'password_resets' => [],
            'permissions'     => [],
            'roles'           => [],
            'user_roles'      => [],
            'users'           => [
                [
                    'id'                   => 1,
                    'type'                 => 'local',
                    'full_name'            => 'Admin',
                    'email'                => 'admin@admin.com',
                    'username'             => 'admin',
                    'is_super_user'        => false,
                    'is_banned'            => false,
                    'password_hash'        => 'hash',
                    'password_algorithm'   => 'bcrypt',
                    'password_cost_factor' => 10,
                    'remember_token'       => null,
                    'oauth_provider_name'  => null,
                    'oauth_account_id'     => null,
                ],
            ],
        ]);
    }

    public function testSaveOauthAdmin()
    {
        $this->userRepo->save(new OauthAdmin(
            'google',
            'account-id',
            'Admin',
            new EmailAddress('admin@admin.com'),
            'admin'
        ));

        $this->assertDatabaseDataSameAs([
            'password_resets' => [],
            'permissions'     => [],
            'roles'           => [],
            'user_roles'      => [],
            'users'           => [
                [
                    'id'                   => 1,
                    'type'                 => 'oauth',
                    'full_name'            => 'Admin',
                    'email'                => 'admin@admin.com',
                    'username'             => 'admin',
                    'is_super_user'        => false,
                    'is_banned'            => false,
                    'password_hash'        => null,
                    'password_algorithm'   => null,
                    'password_cost_factor' => null,
                    'remember_token'       => null,
                    'oauth_provider_name'  => 'google',
                    'oauth_account_id'     => 'account-id',
                ],
            ],
        ]);
    }

    public function testLoadUser()
    {
        $this->setDataInDb([
            'password_resets' => [],
            'permissions'     => [],
            'roles'           => [],
            'user_roles'      => [],
            'users'           => [
                [
                    'id'                   => 1,
                    'type'                 => 'local',
                    'full_name'            => 'Admin',
                    'email'                => 'admin@admin.com',
                    'username'             => 'admin',
                    'is_super_user'        => false,
                    'is_banned'            => false,
                    'password_hash'        => 'hash',
                    'password_algorithm'   => 'bcrypt',
                    'password_cost_factor' => 10,
                    'remember_token'       => null,
                    'oauth_provider_name'  => null,
                    'oauth_account_id'     => null,
                ],
            ],
        ]);

        $expected = new LocalAdmin(
            'Admin',
            new EmailAddress('admin@admin.com'),
            'admin',
            new HashedPassword('hash', 'bcrypt', 10)
        );
        $expected->setId(1);

        $this->assertEquals($expected, $this->userRepo->get(1));
    }

    public function testPasswordResetTokenSave()
    {
        $this->passwordResetTokenRepo->save(new PasswordResetToken('test@email.com', 'token', DateTime::fromString('2000-01-01 12:00:00')));

        $this->assertDatabaseDataSameAs([
            'password_resets' => [
                ['id' => 1, 'email' => 'test@email.com', 'token' => 'token', 'created_at' => '2000-01-01 12:00:00'],
            ],
            'permissions'     => [],
            'roles'           => [],
            'user_roles'      => [],
            'users'           => [],
        ]);
    }

    public function testLoadPasswordResetToken()
    {
        $this->setDataInDb([
            'password_resets' => [
                ['id' => 1, 'email' => 'test@email.com', 'token' => 'token', 'created_at' => '2000-01-01 12:00:00'],
            ],
        ]);

        $expected = new PasswordResetToken(
            'test@email.com',
            'token',
            DateTime::fromString('2000-01-01 12:00:00')
        );
        $expected->setId(1);

        $this->assertEquals($expected, $this->passwordResetTokenRepo->get(1));
    }

    public function testSaveRole()
    {
        $this->roleRepo->save(new Role(
            'admin',
            Permission::collectionFromNames(['a', 'b', 'c'])
        ));

        $this->assertDatabaseDataSameAs([
            'password_resets' => [],
            'roles'           => [
                ['id' => 1, 'name' => 'admin'],
            ],
            'permissions'     => [
                ['id' => 1, 'role_id' => 1, 'name' => 'a'],
                ['id' => 2, 'role_id' => 1, 'name' => 'b'],
                ['id' => 3, 'role_id' => 1, 'name' => 'c'],
            ],
            'user_roles'      => [],
            'users'           => [],
        ]);
    }

    public function testLoadRole()
    {
        $this->setDataInDb([
            'roles'       => [
                ['id' => 1, 'name' => 'admin'],
            ],
            'permissions' => [
                ['id' => 1, 'role_id' => 1, 'name' => 'a'],
                ['id' => 2, 'role_id' => 1, 'name' => 'b'],
                ['id' => 3, 'role_id' => 1, 'name' => 'c'],
            ],
        ]);

        $expected = new Role(
            'admin',
            Permission::collectionFromNames(['a', 'b', 'c'])
        );
        $expected->setId(1);

        $this->assertEquals($expected, $this->roleRepo->get(1));
    }

    public function testAssociateUserToRole()
    {
        $user = new LocalAdmin('Admin', new EmailAddress('admin@admin.com'), 'admin', new HashedPassword('hash', 'bcrypt', 10));
        $role = new Role('admin', Permission::collection());

        $this->userRepo->save($user);
        $this->roleRepo->save($role);

        $user->giveRole($role);

        $this->userRepo->save($user);

        $this->assertDatabaseDataSameAs([
            'password_resets' => [],
            'permissions'     => [],
            'users'           => [
                [
                    'id'                   => 1,
                    'type'                 => 'local',
                    'full_name'            => 'Admin',
                    'email'                => 'admin@admin.com',
                    'username'             => 'admin',
                    'is_super_user'        => false,
                    'is_banned'            => false,
                    'password_hash'        => 'hash',
                    'password_algorithm'   => 'bcrypt',
                    'password_cost_factor' => 10,
                    'remember_token'       => null,
                    'oauth_provider_name'  => null,
                    'oauth_account_id'     => null,
                ],
            ],
            'roles'           => [
                ['id' => 1, 'name' => 'admin'],
            ],
            'user_roles'      => [
                ['id' => 1, 'role_id' => 1, 'user_id' => 1],
            ],
        ]);
    }

    public function testLoadRoleIds()
    {
        $this->setDataInDb([
            'password_resets' => [],
            'permissions'     => [],
            'users'           => [
                [
                    'id'                   => 1,
                    'type'                 => 'local',
                    'full_name'            => 'Admin',
                    'email'                => 'admin@admin.com',
                    'username'             => 'admin',
                    'is_super_user'        => false,
                    'is_banned'            => false,
                    'password_hash'        => 'hash',
                    'password_algorithm'   => 'bcrypt',
                    'password_cost_factor' => 10,
                    'remember_token'       => null,
                    'oauth_provider_name'  => null,
                    'oauth_account_id'     => null,
                ],
            ],
            'roles'           => [
                ['id' => 10, 'name' => 'admin'],
            ],
            'user_roles'      => [
                ['id' => 1, 'role_id' => 10, 'user_id' => 1],
            ],
        ]);

        $expected                 = new LocalAdmin(
            'Admin',
            new EmailAddress('admin@admin.com'),
            'admin',
            new HashedPassword('hash', 'bcrypt', 10)
        );
        $expected->getRoleIds()[] = 10;
        $expected->setId(1);

        $this->assertEquals($expected, $this->userRepo->get(1));
    }
}
