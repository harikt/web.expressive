<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence;

use Dms\Core\Auth\IRoleRepository;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Web\Expressive\Auth\Role;

/**
 * The role repository interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RoleRepository extends DbRepository implements IRoleRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(Role::class));
    }
}
