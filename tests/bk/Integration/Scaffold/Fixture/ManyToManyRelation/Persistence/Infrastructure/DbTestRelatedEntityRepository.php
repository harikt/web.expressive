<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Infrastructure;

use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Persistence\Services\ITestRelatedEntityRepository;
use Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity;

/**
 * The database repository implementation for the Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ManyToManyRelation\Domain\TestRelatedEntity entity.
 */
class DbTestRelatedEntityRepository extends DbRepository implements ITestRelatedEntityRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(TestRelatedEntity::class));
    }
}
