<?php

namespace Dms\Web\Expressive\Persistence\Db;

use Dms\Core\Persistence\Db\Connection\IQuery;
use Dms\Core\Persistence\Db\Doctrine\DoctrineConnection;
use Illuminate\Database\Connection as IlluminateConnection;

/**
 * The laravel connection class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LaravelConnection extends DoctrineConnection
{
    /**
     * @var IlluminateConnection
     */
    private $connection;

    public function __construct(IlluminateConnection $connection)
    {
        parent::__construct($connection->getDoctrineConnection());
        $this->connection = $connection;
        $connection->getPdo()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function prepare($sql, array $parameters = []) : IQuery
    {
        return new LaravelLoggingQuery($this, $this->connection, $sql, $parameters, parent::prepare($sql, $parameters));
    }
}
