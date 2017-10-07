<?php

namespace Dms\Web\Expressive\Tests\Integration\Fixtures\Demo;

use Dms\Core\Persistence\Db\Mapping\Definition\Orm\OrmDefinition;
use Dms\Core\Persistence\Db\Mapping\Orm;
use Dms\Web\Expressive\Persistence\Db\DmsOrm;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DemoOrm extends Orm
{
    /**
     * Defines the object mappers registered in the orm.
     *
     * @param OrmDefinition $orm
     *
     * @return void
     */
    protected function define(OrmDefinition $orm)
    {
        $orm->encompass(DmsOrm::inDefaultNamespace());
    }
}
