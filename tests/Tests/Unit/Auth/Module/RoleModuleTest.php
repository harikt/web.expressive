<?php

namespace Dms\Web\Expressive\Tests\Unit\Auth\Module;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Auth\IHashedPassword;
use Dms\Core\Auth\IPermission;
use Dms\Core\Auth\IRoleRepository;
use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\Permission;
use Dms\Core\Common\Crud\Action\Object\IObjectAction;
use Dms\Core\Common\Crud\ICrudModule;
use Dms\Core\ICms;
use Dms\Core\Model\EntityIdCollection;
use Dms\Core\Model\IMutableObjectSet;
use Dms\Core\Persistence\ArrayRepository;
use Dms\Core\Persistence\IRepository;
use Dms\Core\Tests\Common\Crud\Modules\CrudModuleTest;
use Dms\Core\Tests\Module\Mock\MockAuthSystem;
use Dms\Web\Expressive\Auth\LocalAdmin;
use Dms\Web\Expressive\Auth\Module\RoleModule;
use Dms\Web\Expressive\Auth\Role;
use Dms\Web\Expressive\Auth\Admin;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RoleModuleTest extends CrudModuleTest
{
    /**
     * @return IMutableObjectSet
     */
    protected function buildRepositoryDataSource() : IMutableObjectSet
    {
        $adminRole = new Role('admin', Permission::collection([Permission::named('a'), Permission::named('b')]), new EntityIdCollection([1]));
        $adminRole->setId(1);

        $defaultRole = new Role('default', Permission::collection([Permission::named('b')]), new EntityIdCollection([2]));
        $defaultRole->setId(2);


        return new class(Role::collection([$adminRole, $defaultRole])) extends ArrayRepository implements IRoleRepository {
        };
    }

    /**
     * @param IMutableObjectSet    $dataSource
     * @param MockAuthSystem $authSystem
     *
     * @return ICrudModule
     */
    protected function buildCrudModule(IMutableObjectSet $dataSource, MockAuthSystem $authSystem) : ICrudModule
    {
        return new RoleModule($dataSource, $this->mockUserDataSource(), $authSystem, $this->mockCms());
    }

    /**
     * @return IPermission[]
     */
    protected function expectedReadModuleRequiredPermissions()
    {
        return [];
    }

    protected function mockUserDataSource() : IAdminRepository
    {
        $admin = new LocalAdmin('test', new EmailAddress('admin@admin.com'), 'admin', $this->getMockForAbstractClass(IHashedPassword::class));
        $admin->setId(1);

        $person = new LocalAdmin('test', new EmailAddress('person@person.com'), 'person', $this->getMockForAbstractClass(IHashedPassword::class));
        $person->setId(2);

        return new class(Admin::collection([$admin, $person])) extends ArrayRepository implements IAdminRepository {
        };
    }

    protected function mockCms() : ICms
    {
        $mock = $this->getMockForAbstractClass(ICms::class);
        $mock->method('loadPermissions')
            ->willReturn([
                Permission::named('a'),
                Permission::named('b'),
            ]);

        return $mock;
    }

    /**
     * @return string
     */
    protected function expectedName()
    {
        return 'roles';
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

    public function testCreate()
    {
        $action = $this->module->getCreateAction();

        $action->run([
            'name'        => 'another',
            'permissions' => ['a'],
            'users'       => ['1', '2'],
        ]);

        $role = new Role('another', Permission::collectionFromNames(['a']), new EntityIdCollection([1, 2]));
        $role->setId(3);

        $this->assertEquals($role, $this->dataSource->get(3));
    }

    public function testCreateWithNoUsers()
    {
        $action = $this->module->getCreateAction();

        $action->run([
            'name'        => 'another',
            'permissions' => ['a'],
        ]);

        $role = new Role('another', Permission::collectionFromNames(['a']), new EntityIdCollection([]));
        $role->setId(3);

        $this->assertEquals($role, $this->dataSource->get(3));
    }

    public function testEdit()
    {
        $action = $this->module->getEditAction();

        $action->run([
            IObjectAction::OBJECT_FIELD_NAME => 2,
            'name'                           => 'edit',
            'permissions'                    => ['a'],
            'users'                          => ['1', '2'],
        ]);

        /** @var Role $role */
        $role = $this->dataSource->get(2);

        $this->assertSame('edit', $role->getName());
        $this->assertEquals(Permission::collectionFromNames(['a']), $role->getPermissions());
        $this->assertEquals(new EntityIdCollection([1, 2]), $role->getUserIds());
    }
}
