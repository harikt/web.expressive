<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence\Mapper;

use Dms\Common\Structure\DateTime\Persistence\DateTimeMapper;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Web\Expressive\Auth\Password\PasswordResetToken;

/**
 * The password reset token entity mapper.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PasswordResetTokenMapper extends EntityMapper
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
        // This should mirror the default table created by laravel
        // for password resets.

        $map->type(PasswordResetToken::class);
        $map->toTable('password_resets');

        $map->idToPrimaryKey('id');

        $map->property(PasswordResetToken::EMAIL)
            ->to('email')
            ->asVarchar(255);

        $map->property(PasswordResetToken::TOKEN)
            ->to('token')
            ->unique()
            ->asVarchar(255);

        $map->embedded(PasswordResetToken::CREATED_AT)
            ->using(new DateTimeMapper('created_at'));
    }
}
