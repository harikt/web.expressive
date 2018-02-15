<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Module;

use Dms\Common\Structure\Field;
use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\IRoleRepository;
use Dms\Core\Auth\Permission;
use Dms\Core\Common\Crud\CrudModule;
use Dms\Core\Common\Crud\Definition\CrudModuleDefinition;
use Dms\Core\Common\Crud\Definition\Form\CrudFormDefinition;
use Dms\Core\Common\Crud\Definition\Table\SummaryTableDefinition;
use Dms\Core\ICms;
use Dms\Core\Model\EntityIdCollection;
use Dms\Core\Model\ValueObjectCollection;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\Role;

/**
 * The role crud module.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RoleModule extends CrudModule
{
    /**
     * @var IAdminRepository
     */
    private $userRepo;

    /**
     * @var ICms
     */
    private $cms;

    /**
     * RoleModule constructor.
     *
     * @param IRoleRepository  $dataSource
     * @param IAdminRepository $userRepo
     * @param IAuthSystem      $authSystem
     * @param ICms             $cms
     */
    public function __construct(
        IRoleRepository $dataSource,
        IAdminRepository $userRepo,
        IAuthSystem $authSystem,
        ICms $cms
    ) {
        $this->cms      = $cms;
        $this->userRepo = $userRepo;

        parent::__construct($dataSource, $authSystem);
    }

    /**
     * Defines the structure of this module.
     *
     * @param CrudModuleDefinition $module
     */
    protected function defineCrudModule(CrudModuleDefinition $module)
    {
        $module->name('roles');

        $module->metadata(
            [
            'icon' => 'lock'
            ]
        );

        $module->labelObjects()->fromProperty(Role::NAME);

        $module->crudForm(
            function (CrudFormDefinition $form) {
                $permissionOptions = $this->loadPermissionOptions();

                $form->section(
                    'Details',
                    [
                    //
                    $form->field(
                        Field::create('name', 'Name')
                        ->string()
                        ->required()
                        ->maxLength(100)
                    )->bindToProperty(Role::NAME),
                    //
                    $form->field(
                        Field::create('permissions', 'Permissions')
                        ->multipleFrom($permissionOptions)
                        ->required()
                        ->minLength(1)
                        ->map(
                            function (array $names) {
                                return Permission::collectionFromNames($names);
                            },
                            function (ValueObjectCollection $collection) {
                                return $collection
                                    ->select(
                                        function (Permission $permission) {
                                            return $permission->getName();
                                        }
                                    )
                                ->asArray();
                            },
                            Permission::collectionType()
                        )
                    )->bindToProperty(Role::PERMISSIONS),
                    //
                    $form->field(
                        Field::create('users', 'Users')
                        ->entityIdsFrom($this->userRepo)
                        ->defaultTo([])
                        ->labelledBy(Admin::FULL_NAME)
                        ->mapToCollection(EntityIdCollection::type())
                    )->bindToProperty(Role::USER_IDS),
                    ]
                );
            }
        );

        $module->removeAction()->deleteFromDataSource();

        $module->summaryTable(
            function (SummaryTableDefinition $table) {
                $table->mapProperty(Role::NAME)->to(Field::create('name', 'Name')->string());

                $table->mapProperty(Role::PERMISSIONS . '.count()')->to(Field::create('permissions', '# Permissions')->int());

                $table->mapProperty(Role::USER_IDS . '.count()')->to(Field::create('users', '# Users')->int());

                $table->view('all', 'All')
                    ->asDefault()
                    ->loadAll()
                    ->orderByAsc(Role::NAME);
            }
        );

        $module->widget('summary-table')->label('Roles')
            ->withTable('summary-table')
            ->allRows();
    }

    /**
     * @return array
     */
    protected function loadPermissionOptions() : array
    {
        $permissionOptions = [];

        $allPermissions = array_merge(
            [
            Permission::named('admin.roles.view'),
            Permission::named('admin.roles.create'),
            Permission::named('admin.roles.edit'),
            Permission::named('admin.roles.remove'),
            ],
            $this->cms->loadPermissions()
        );

        foreach ($allPermissions as $permission) {
            $permissionOptions[$permission->getName()] = ucwords(
                strtr($permission->getName(), ['.' => ' :: ', '-' => ' '])
            );
        }

        return $permissionOptions;
    }
}
