<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Persistence;

use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Web\Expressive\Auth\Admin;

/**
 * The laravel user repository.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminRepository extends DbRepository implements IAdminRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(Admin::class));
    }
}
