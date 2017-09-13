<?php

namespace Dms\Web\Expressive\Persistence\Db;

use Dms\Core\Persistence\Db\Connection\IConnection;
use Dms\Core\Persistence\Db\Connection\IQuery;
use Dms\Core\Persistence\Db\Connection\Query;
use Illuminate\Database\Connection as IlluminateConnection;

/**
 * The logging laravel query.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LaravelLoggingQuery extends Query
{
    /**
     * @var IlluminateConnection
     */
    private $laravelConnection;

    /**
     * @var string
     */
    private $sql;

    /**
     * @var IQuery
     */
    private $innerQuery;
    /**
     * @var array
     */
    private $bindings;

    /**
     * LaravelLoggingQuery constructor.
     *
     * @param IConnection|IlluminateConnection $connection
     * @param IlluminateConnection             $laravelConnection
     * @param string                           $sql
     * @param array                            $bindings
     * @param IQuery                           $innerQuery
     */
    public function __construct(IConnection $connection, IlluminateConnection $laravelConnection, string $sql, array $bindings, IQuery $innerQuery)
    {
        parent::__construct($connection);
        $this->laravelConnection = $laravelConnection;
        $this->sql               = $sql;
        $this->bindings          = $bindings;
        $this->innerQuery        = $innerQuery;
    }

    /**
     * @param int|string $parameter
     * @param mixed      $value
     *
     * @return void
     */
    protected function doSetParameter($parameter, $value)
    {
        $this->bindings[$parameter] = $value;
        $this->innerQuery->setParameter($parameter, $value);
    }

    /**
     * @return void
     */
    protected function doExecute()
    {
        $this->laravelConnection->logQuery($this->sql, $this->bindings);
        $this->innerQuery->execute();
    }

    /**
     * @return int
     */
    protected function loadAffectedRows() : int
    {
        return $this->innerQuery->getAffectedRows();
    }

    /**
     * @return array[]
     */
    protected function loadResults() : array
    {
        return $this->innerQuery->getResults();
    }
}
