<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Persistence\Db;

use Dms\Common\Structure\CommonOrm;
use Dms\Core\Persistence\Db\Mapping\Definition\Orm\OrmDefinition;
use Dms\Core\Persistence\Db\Mapping\Orm;
use Dms\Package\Analytics\Persistence\AnalyticsOrm;
use Dms\Web\Expressive\Auth\Persistence\AuthOrm;
use Dms\Web\Expressive\File\Persistence\TempFileOrm;

/**
 * The standard dms orm.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsOrm extends Orm
{
    const NAMESPACE = 'dms_';

    /**
     * Defines the object mappers registered in the orm.
     *
     * @param OrmDefinition $orm
     *
     * @return void
     */
    protected function define(OrmDefinition $orm)
    {
        $orm->encompassAll(
            [
            new CommonOrm(),
            new AuthOrm(),
            new TempFileOrm(),
            // new AnalyticsOrm(),
            ]
        );
    }

    /**
     * @return DmsOrm
     */
    public static function inDefaultNamespace() : DmsOrm
    {
        return (new self())->inNamespace(self::NAMESPACE);
    }
}
