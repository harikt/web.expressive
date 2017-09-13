<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File\Persistence;

use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Mapping\IOrm;
use Dms\Core\Persistence\DbRepository;
use Dms\Web\Expressive\File\TemporaryFile;

/**
 * The temporary file repository.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TemporaryFileRepository extends DbRepository implements ITemporaryFileRepository
{
    public function __construct(IConnection $connection, IOrm $orm)
    {
        parent::__construct($connection, $orm->getEntityMapper(TemporaryFile::class));
    }
}
