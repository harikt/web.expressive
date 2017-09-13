<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Util;

use Dms\Core\Module\IModule;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ModuleLabeler
{
    /**
     * @param IModule $module
     *
     * @return string
     */
    public static function getModuleTitle(IModule $module) : string
    {
        return $module->getMetadata('label') ?? StringHumanizer::title($module->getName());
    }
}
