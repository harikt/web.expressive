<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence\Mapper;

use Dms\Common\Structure\Web\Persistence\EmailAddressMapper;
use Dms\Core\Persistence\Db\Mapping\Definition\MapperDefinition;
use Dms\Core\Persistence\Db\Mapping\EntityMapper;
use Dms\Library\Metadata\Infrastructure\Persistence\MetadataMapper;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\LocalAdmin;
use Dms\Web\Expressive\Auth\OauthAdmin;
use Dms\Web\Expressive\Auth\Role;

/**
 * The user entity mapper.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminMapper extends EntityMapper
{
    const AUTH_IDENTIFIER_COLUMN = 'id';
    const AUTH_PASSWORD_COLUMN = 'password_hash';
    const AUTH_REMEMBER_TOKEN_COLUMN = 'remember_token';

    /**
     * Defines the entity mapper
     *
     * @param MapperDefinition $map
     *
     * @return void
     */
    protected function define(MapperDefinition $map)
    {
        $map->type(Admin::class);
        $map->toTable('users');

        $map->idToPrimaryKey(self::AUTH_IDENTIFIER_COLUMN);

        $map->column('type')->asEnum(['local', 'oauth']);

        $map->property(Admin::FULL_NAME)
            ->to('full_name')
            ->asVarchar(255);

        $map->embedded(Admin::EMAIL_ADDRESS)
            ->unique()
            ->using(new EmailAddressMapper('email'));

        $map->property(Admin::USERNAME)
            ->to('username')
            ->unique()
            ->asVarchar(255);


        $map->property(Admin::IS_SUPER_USER)
            ->to('is_super_user')
            ->asBool();

        $map->property(Admin::IS_BANNED)
            ->to('is_banned')
            ->asBool();

        $map->relation(Admin::ROLE_IDS)
            ->to(Role::class)
            ->toManyIds()
            ->withBidirectionalRelation(Role::USER_IDS)
            ->throughJoinTable('user_roles')
            ->withParentIdAs('user_id')
            ->withRelatedIdAs('role_id');

        $map->subclass()->withTypeInColumn('type', 'local')->define(
            function (MapperDefinition $map) {
                $map->type(LocalAdmin::class);

                $map->embedded(LocalAdmin::PASSWORD)
                    ->withColumnsPrefixedBy('password_')
                    ->using(new HashedPasswordMapper());

                $map->property(LocalAdmin::REMEMBER_TOKEN)
                    ->to(self::AUTH_REMEMBER_TOKEN_COLUMN)
                    ->nullable()
                    ->asVarchar(255);
            }
        );

        $map->subclass()->withTypeInColumn('type', 'oauth')->define(
            function (MapperDefinition $map) {
                $map->type(OauthAdmin::class);

                $map->property(OauthAdmin::OAUTH_PROVIDER_NAME)
                    ->to('oauth_provider_name')
                    ->asVarchar(255);

                $map->property(OauthAdmin::OAUTH_ACCOUNT_ID)
                    ->to('oauth_account_id')
                    ->asVarchar(255);
            }
        );

        MetadataMapper::mapMetadataToJsonColumn($map, 'metadata');
    }
}
