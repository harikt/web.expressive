<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Persistence\Services\ITestRelatedEntityRepository;
use Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestRelatedEntity;

/**
 * The database repository implementation for the Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ToOneRelation\Domain\TestRelatedEntity entity.
 */
class DbTestRelatedEntityRepository extends DbRepository implements ITestRelatedEntityRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(TestRelatedEntity::class));
    }
}