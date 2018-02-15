<?php

namespace Dms\Web\Expressive\Util;

use Dms\Core\Common\Crud\IReadModule;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Util\Debug;
use Illuminate\Cache\Repository as Cache;

/**
 * The entity module map class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EntityModuleMap
{
    const CACHE_EXPIRY = 60;

    /**
     * @var ICms
     */
    protected $cms;

    /**
     * @var array
     */
    protected $map;

    /**
     * EntityModuleMap constructor.
     *
     * @param ICms  $cms
     * @param Cache $cache
     */
    public function __construct(ICms $cms, Cache $cache)
    {
        $installedModulesHash = md5(implode('__', $cms->getPackageNames()));
        $moduleMapKey         = 'dms:module-map:' . $installedModulesHash;

        $this->cms = $cms;
        $this->map = $cache->remember(
            $moduleMapKey,
            self::CACHE_EXPIRY,
            function () {
                $map = [];

                foreach ($this->cms->loadPackages() as $package) {
                    foreach ($package->loadModules() as $module) {
                        if ($module instanceof IReadModule) {
                            $map[$module->getObjectType()] = $package->getName() . '.' . $module->getName();
                        }
                    }
                }

                return $map;
            }
        );
    }

    /**
     * @param string $entityType
     *
     * @return IReadModule
     * @throws InvalidArgumentException
     */
    public function loadModuleFor(string $entityType) : IReadModule
    {
        $name = $this->getPackageAndModuleName($entityType);

        if (!$name) {
            throw InvalidArgumentException::format(
                'Invalid call to %s: unknown entity type, expecting one of (%s), %s given',
                __METHOD__,
                Debug::formatValues(array_keys($this->map)),
                $entityType
            );
        }

        list($packageName, $moduleName) = explode('.', $name);

        return $this->cms->loadPackage($packageName)->loadModule($moduleName);
    }

    public function hasModuleFor(string $entityType)
    {
        return $this->getPackageAndModuleName($entityType) !== null;
    }

    /**
     * @param string $entityType
     *
     * @return string|null
     * @throws InvalidArgumentException
     */
    protected function getPackageAndModuleName(string $entityType)
    {
        while (!isset($this->map[$entityType])) {
            $entityType = get_parent_class($entityType);

            if ($entityType === false) {
                return null;
            }
        }

        return $this->map[$entityType];
    }
}
