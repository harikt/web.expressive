<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Util;

use Dms\Core\Package\IPackage;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PackageLabeler
{
    /**
     * @param IPackage $package
     *
     * @return string
     */
    public static function getPackageTitle(IPackage $package) : string
    {
        return $package->getMetadata('label') ?? StringHumanizer::title($package->getName());
    }
}
