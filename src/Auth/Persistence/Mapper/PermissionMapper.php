<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence\Mapper;

use Dms\Core\Auth\Permission;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;

/**
 * The permission value object mapper.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PermissionMapper extends IndependentValueObjectMapper
{
    /**
     * Defines the value object mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        $map->type(Permission::class);

        $map->property(Permission::NAME)->to('name')->asVarchar(255);
    }
}
