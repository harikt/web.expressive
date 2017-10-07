<?php

namespace Dms\Web\Expressive\Tests\Unit\Auth\Module;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\IHashedPassword;
use Dms\Core\Auth\IPermission;
use Dms\Core\Auth\IRoleRepository;
use Dms\Core\Auth\Permission;
use Dms\Core\Common\Crud\Action\Object\IObjectAction;
use Dms\Core\Common\Crud\ICrudModule;
use Dms\Core\Form\InvalidFormSubmissionException;
use Dms\Core\Model\EntityIdCollection;
use Dms\Core\Model\IMutableObjectSet;
use Dms\Core\Persistence\ArrayRepository;
use Dms\Core\Tests\Common\Crud\Modules\CrudModuleTest;
use Dms\Core\Tests\Module\Mock\MockAuthSystem;
use Dms\Core\Widget\TableWidget;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\LocalAdmin;
use Dms\Web\Expressive\Auth\Module\AdminUserModule;
use Dms\Web\Expressive\Auth\OauthAdmin;
use Dms\Web\Expressive\Auth\Password\HashedPassword;
use Dms\Web\Expressive\Auth\Password\IPasswordHasher;
use Dms\Web\Expressive\Auth\Password\IPasswordHasherFactory;
use Dms\Web\Expressive\Auth\Password\IPasswordResetService;
use Dms\Web\Expressive\Auth\Role;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminUserModuleTest extends CrudModuleTest
{
    /**
     * @var bool
     */
    protected $hasPasswordResetServiceBeenCalled = false;

    /**
     * @var bool
     */
    protected $hasHasherBeenCalled = false;

    /**
     * @return IMutableObjectSet
     */
    protected function buildRepositoryDataSource() : IMutableObjectSet
    {
        $admin = new LocalAdmin('Test', new EmailAddress('admin@admin.com'), 'admin', $this->getMockForAbstractClass(IHashedPassword::class));
        $admin->setId(1);

        $person = new LocalAdmin('Person', new EmailAddress('person@person.com'), 'person', $this->getMockForAbstractClass(IHashedPassword::class));
        $person->setId(2);

        $oauthAdmin = new OauthAdmin('google', '1233', 'Google Guy', new EmailAddress('person@person.com'), 'person');
        $oauthAdmin->setId(3);

        return new class(Admin::collection([$admin, $person, $oauthAdmin])) extends ArrayRepository implements IAdminRepository {
        };
    }

    /**
     * @param IMutableObjectSet $dataSource
     * @param MockAuthSystem    $authSystem
     *
     * @return ICrudModule
     */
    protected function buildCrudModule(IMutableObjectSet $dataSource, MockAuthSystem $authSystem) : ICrudModule
    {
        return new AdminUserModule(
            $dataSource,
            $this->mockRolDataSource(),
            $this->mockPasswordHasherFactory(),
            $authSystem,
            $this->mockPasswordResetService()
        );
    }

    /**
     * @return IPermission[]
     */
    protected function expectedReadModuleRequiredPermissions()
    {
        return [];
    }

    protected function mockRolDataSource() : IRoleRepository
    {
        $adminRole = new Role('admin', Permission::collection([Permission::named('a'), Permission::named('b')]), new EntityIdCollection([1]));
        $adminRole->setId(1);

        $defaultRole = new Role('default', Permission::collection([Permission::named('b')]), new EntityIdCollection([2]));
        $defaultRole->setId(2);


        return new class(Role::collection([$adminRole, $defaultRole])) extends ArrayRepository implements IRoleRepository {
        };
    }

    protected function mockPasswordResetService() : IPasswordResetService
    {
        $passwordResetService = $this->getMockForAbstractClass(IPasswordResetService::class);
        $passwordResetService->method('resetUserPassword')
            ->willReturnCallback(function () {
                $this->hasPasswordResetServiceBeenCalled = true;
            });

        return $passwordResetService;
    }

    protected function mockPasswordHasherFactory() : IPasswordHasherFactory
    {
        $hasherFactory = $this->getMockForAbstractClass(IPasswordHasherFactory::class);
        $hasherFactory->method('buildDefault')
            ->willReturnCallback(function () {
                $hasher = $this->getMockForAbstractClass(IPasswordHasher::class);
                $hasher->method('hash')
                    ->willReturnCallback(function () {
                        $this->hasHasherBeenCalled = true;

                        return $this->createMock(HashedPassword::class);
                    });

                return $hasher;
            });

        return $hasherFactory;
    }

    /**
     * @return string
     */
    protected function expectedName()
    {
        return 'users';
    }

    /**
     * @return IPermission[]
     */
    protected function expectedReadModulePermissions()
    {
        return [
            Permission::named('create'),
            Permission::named('edit'),
            Permission::named('remove'),
        ];
    }

    public function testResetUserPasswordAction()
    {
        $action = $this->module->getParameterizedAction('reset-password');

        $this->assertThrows(function () use ($action) {
            $action->run([
                IObjectAction::OBJECT_FIELD_NAME => 1,
                'new_password'                   => 'abc123A',
                'new_password_confirmation'      => 'does-not-match',
            ]);
        }, InvalidFormSubmissionException::class);

        $this->assertFalse($this->hasPasswordResetServiceBeenCalled);

        $action->run([
            IObjectAction::OBJECT_FIELD_NAME => 1,
            'new_password'                   => 'abc123A',
            'new_password_confirmation'      => 'abc123A',
        ]);
        $this->assertTrue($this->hasPasswordResetServiceBeenCalled);
    }

    public function testCreateUser()
    {
        $action = $this->module->getCreateAction();

        $adminRoleId = 1;
        /** @var Admin $user */
        $user = $action->run([
            'name'     => 'New User',
            'email'    => 'new@user.com',
            'username' => 'user',
            'password' => 'abc123',
            'roles'    => [$adminRoleId],
        ]);

        $this->assertInstanceOf(LocalAdmin::class, $user);

        $this->assertSame('New User', $user->getFullName());
        $this->assertSame('new@user.com', $user->getEmailAddress());
        $this->assertSame('user', $user->getUsername());
        $this->assertTrue($this->hasHasherBeenCalled);
        $this->assertInstanceOf(HashedPassword::class, $user->getPassword());
        $this->assertSame([$adminRoleId], $user->getRoleIds()->asArray());
    }

    public function testEmailAddressMustBeUnique()
    {
        $action = $this->module->getEditAction();

        $adminRoleId = 1;

        // Not changing the email should be fine as it is still unique
        $action->run([
            IObjectAction::OBJECT_FIELD_NAME => 1,
            'name'                           => 'Admin',
            'email'                          => 'admin@admin.com',
            'username'                       => 'admin',
            'roles'                          => [$adminRoleId],
        ]);

        $this->assertThrows(function () use ($action, $adminRoleId) {
            $action->run([
                IObjectAction::OBJECT_FIELD_NAME => 1,
                'name'                           => 'Admin',
                'email'                          => 'person@person.com', // This should cause a duplicate
                'username'                       => 'admin',
                'roles'                          => [$adminRoleId],
            ]);
        }, InvalidFormSubmissionException::class);

        // Changing it to something new should be fine
        $action->run([
            IObjectAction::OBJECT_FIELD_NAME => 1,
            'name'                           => 'Admin',
            'email'                          => 'some-new-email@admin.com',
            'username'                       => 'admin',
            'roles'                          => [$adminRoleId],
        ]);
    }

    public function testSummaryTableWidget()
    {
        /** @var TableWidget $widget */
        $widget = $this->module->getWidget('summary-table');

        $this->assertSame('summary-table', $widget->getName());
        $this->assertInstanceOf(TableWidget::class, $widget);
        $this->assertCount(3, $widget->loadData()->getSections()[0]->getRows());
    }
}
