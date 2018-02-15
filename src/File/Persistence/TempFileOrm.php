<?php declare(strict_types=1);

namespace Dms\Web\Expressive\File\Persistence;

use Dms\Core\Persistence\Db\Mapping\Definition\Orm\OrmDefinition;
use Dms\Core\Persistence\Db\Mapping\Orm;
use Dms\Web\Expressive\File\TemporaryFile;

/**
 * The temp file orm
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TempFileOrm extends Orm
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
        $orm->entities(
            [
            TemporaryFile::class => TemporaryFileMapper::class,
            ]
        );
    }
}
