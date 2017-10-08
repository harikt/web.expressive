<?php

namespace Dms\Web\Expressive\View;

use Dms\Core\Auth\IPermission;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Module\IModule;
use Dms\Core\Package\IDashboard;
use Dms\Core\Package\IPackage;
use Dms\Web\Expressive\Auth\LaravelAuthSystem;
use Dms\Web\Expressive\Util\ModuleLabeler;
use Dms\Web\Expressive\Util\PackageLabeler;
use Illuminate\Cache\Repository as Cache;
use Illuminate\View\View;

/**
 * The dms navigation view composer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsNavigationViewComposer
{
    const NAVIGATION_CACHE_EXPIRY_MINUTES = 60;

    /**
     * @var ICms
     */
    protected $cms;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * DmsNavigationViewComposer constructor.
     *
     * @param ICms  $cms
     * @param Cache $cache
     */
    public function __construct(ICms $cms, Cache $cache)
    {
        $this->cms   = $cms;
        $this->cache = $cache;
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $installedModulesHash = md5(implode('__', $this->cms->getPackageNames()));
        $navigationCacheKey   = 'dms:navigation:' . $installedModulesHash;

        /** @var LaravelAuthSystem $authSystem */
        $authSystem = $this->cms->getAuth();
        $view->with(
            'navigation',
            $this->filterElementsByPermissions(
            $authSystem->getAuthenticatedUser()->isSuperUser(),
            $this->getPermissionNames($authSystem->getUserPermissions()),
            $this->cache->remember(
                $navigationCacheKey,
                self::NAVIGATION_CACHE_EXPIRY_MINUTES,
                function () {
                    return $this->loadNavigation();
                }
            )
        )
        );
    }

    private function filterElementsByPermissions(bool $isSuperUser, array $permissionNames, array $navigationElements) : array
    {
        $navigation = [];

        foreach ($navigationElements as $element) {
            if ($element instanceof NavigationElementGroup) {
                $subNavigation = $this->filterElementsByPermissions($isSuperUser, $permissionNames, $element->getElements());

                if ($subNavigation) {
                    $navigation[] = $element->withElements($subNavigation);
                }
            } elseif ($element instanceof NavigationElement) {
                if ($isSuperUser || $element->shouldDisplay($permissionNames)) {
                    $navigation[] = $element;
                }
            }
        }

        return $navigation;
    }

    private function loadNavigation() : array
    {
        $navigation = [];

        $navigation[] = new NavigationElement('Dashboard', 'dms::index', [], 'tachometer');

        foreach ($this->cms->loadPackages() as $package) {
            $packageNavigation = [];

            if ($package->hasDashboard()) {
                $packageNavigation[] = new NavigationElement(
                    'Dashboard',
                    'dms::package.dashboard',
                    ['package' => $package->getName()],
                    'tachometer',
                    $this->getPermissionGroups($package->loadDashboard()),
                    true
                );
            }

            $packageLabel = PackageLabeler::getPackageTitle($package);

            foreach ($package->loadModules() as $module) {
                $moduleLabel         = ModuleLabeler::getModuleTitle($module);
                $packageNavigation[] = new NavigationElement(
                    $moduleLabel,
                    'dms::package.module.dashboard',
                    [
                        'package' => $package->getName(),
                        'module' => $module->getName()
                    ],
                    $this->getModuleIcon($module),
                    $this->getPermissionNames($module->getRequiredPermissions())
                );
            }

            if (count($packageNavigation) === 1) {
                $navigation[] = $packageNavigation[0];
            } else {
                $navigation[] = new NavigationElementGroup($packageLabel, $this->getPackageIcon($package), $packageNavigation);
            }
        }

        return $navigation;
    }

    /**
     * @param array $permissions
     *
     * @return array
     */
    protected function getPermissionNames(array $permissions) : array
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'permissions', $permissions, IPermission::class);
        $names = [];

        foreach ($permissions as $permission) {
            $names[] = $permission->getName();
        }

        return $names;
    }

    private function getPermissionGroups(IDashboard $dashboard) : array
    {
        $permissionGroups = [];

        foreach ($dashboard->getWidgets() as $widget) {
            $group = [];

            foreach ($widget->getModule()->getRequiredPermissions() as $permission) {
                $group[] = $permission->getName();
            }

            foreach ($widget->getWidget()->getRequiredPermissions() as $permission) {
                $group[] = $permission->getName();
            }

            $permissionGroups[] = array_unique($group, SORT_STRING);
        }

        return $permissionGroups;
    }

    private function getPackageIcon(IPackage $package) : string
    {
        return $package->hasMetadata('icon') ? $package->getMetadata('icon') : 'folder';
    }

    private function getModuleIcon(IModule $module) : string
    {
        return $module->hasMetadata('icon') ? $module->getMetadata('icon') : 'circle-o';
    }
}
