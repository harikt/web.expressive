<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence\Mapper;

use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\IndependentValueObjectMapper;
use Dms\Web\Expressive\Auth\Password\HashedPassword;

/**
 * The hashed password mapper.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class HashedPasswordMapper extends IndependentValueObjectMapper
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
        $map->type(HashedPassword::class);

        $map->property(HashedPassword::HASH)->to('hash')->asVarchar(255);
        $map->property(HashedPassword::ALGORITHM)->to('algorithm')->asVarchar(10);
        $map->property(HashedPassword::COST_FACTOR)->to('cost_factor')->asInt();
    }
}
