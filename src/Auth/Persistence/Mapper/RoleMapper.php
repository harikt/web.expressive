<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence\Mapper;

use Dms\Core\Auth\Permission;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\Role;

/**
 * The role entity mapper.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RoleMapper extends EntityMapper
{
    /**
     * Defines the entity mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        $map->type(Role::class);
        $map->toTable('roles');

        $map->idToPrimaryKey('id');

        $map->property(Role::NAME)
            ->to('name')
            ->asVarchar(255);

        $map->embeddedCollection(Role::PERMISSIONS)
            ->toTable('permissions')
            ->withPrimaryKey('id')
            ->withForeignKeyToParentAs('role_id')
            ->to(Permission::class);

        $map->relation(Role::USER_IDS)
            ->to(Admin::class)
            ->toManyIds()
            ->withBidirectionalRelation(Admin::ROLE_IDS)
            ->throughJoinTable('user_roles')
            ->withParentIdAs('role_id')
            ->withRelatedIdAs('user_id');
    }
}
