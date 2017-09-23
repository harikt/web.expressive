<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http;

use Dms\Core\Module\IModule;
use Dms\Web\Expressive\Util\StringHumanizer;
use Zend\Expressive\Router\RouterInterface;

/**
 * The module context class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ModuleContext
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $rootUrl;

    /**
     * @var string[]
     */
    protected $titles = [];

    /**
     * @var string[]
     */
    protected $breadcrumbs = [];

    /**
     * @var callable
     */
    protected $moduleLoaderCallback;

    /**
     * @var IModule|null
     */
    protected $module;

    /**
     * @var bool
     */
    protected $isSubmodule;

    /**
     * ModuleContext constructor.
     *
     * @param RouterInterface  $router
     * @param string           $rootUrl
     * @param array            $titles
     * @param string[]        $breadcrumbs
     * @param IModule|callable $moduleLoaderCallback
     */
    public function __construct(RouterInterface $router, string $rootUrl, array $titles, array $breadcrumbs, $moduleLoaderCallback, bool $isSubmodule = false)
    {
        $this->router = $router;
        $this->rootUrl      = $rootUrl;
        $this->titles       = $titles;
        $this->breadcrumbs  = $breadcrumbs;
        $this->isSubmodule = $isSubmodule;

        if ($moduleLoaderCallback instanceof IModule) {
            $this->module = $moduleLoaderCallback;
        } else {
            $this->moduleLoaderCallback = $moduleLoaderCallback;
        }
    }

    /**
     * @return bool
     */
    public function isSubmodule(): bool
    {
        return $this->isSubmodule;
    }

    /**
     * @param RouterInterface   $router
     * @param string   $packageName
     * @param string   $moduleName
     * @param callable $moduleLoaderCallback
     *
     * @return ModuleContext
     */
    public static function rootContext(RouterInterface $router, string $packageName, string $moduleName, callable $moduleLoaderCallback) : ModuleContext
    {
        return new ModuleContext(
            $router,
            $router->generateUri('dms::package.module.dashboard', ['package' => $packageName, 'module' => $moduleName]),
            [StringHumanizer::title($packageName), StringHumanizer::title($moduleName)],
            [
                $router->generateUri('dms::index')                                                         => 'Home',
                $router->generateUri('dms::package.dashboard', ['package' => $packageName])                => StringHumanizer::title($packageName),
                $router->generateUri('dms::package.module.dashboard', ['package' => $packageName, 'module' => $moduleName]) => StringHumanizer::title($moduleName),
            ],
            $moduleLoaderCallback
        );
    }

    /**
     * @param RouterInterface  $router
     * @param IModule $module
     *
     * @return ModuleContext
     */
    public static function rootContextForModule(RouterInterface $router, IModule $module) : ModuleContext
    {
        return self::rootContext($router, $module->getPackageName(), $module->getName(), function () use ($module) {
            return $module;
        });
    }

    /**
     * @return string[]
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @return string[]
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * @return IModule
     */
    public function getModule() : IModule
    {
        if (!$this->module) {
            $this->module = call_user_func($this->moduleLoaderCallback);
        }

        return $this->module;
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     */
    public function getUrl(
        string $name,
        array $routeParams = [],
        array $queryParams = [],
        $fragmentIdentifier = null,
        array $options = []
    ) : string {
        $routeParams = array_merge($routeParams, [
            'package' => $this->getModule()->getPackageName(),
            'module' => $this->getModule()->getName(),
        ]);

        $uriString = $this->router->generateUri('dms::package.module.' . $name, $routeParams);

        if (count($queryParams) > 0) {
            $uriString .= '?' . http_build_query($queryParams);
        }

        return $uriString;
    }

    /**
     * @return string
     */
    public function getRootUrl() : string
    {
        return $this->rootUrl;
    }

    /**
     * @param string $title
     * @param string $breadcrumbUrl
     * @param string $breadcrumbName
     *
     * @return ModuleContext
     */
    public function withBreadcrumb(string $title, string $breadcrumbUrl, string $breadcrumbName = null) : ModuleContext
    {
        return new ModuleContext(
            $this->router,
            $this->rootUrl,
            array_merge($this->titles, [$title]),
            $this->breadcrumbs + [$breadcrumbUrl => $breadcrumbName ?? $title],
            $this->module ?? $this->moduleLoaderCallback,
            $this->isSubmodule
        );
    }

    /**
     * @param IModule $module
     * @param string  $moduleRootPath
     *
     * @return ModuleContext
     */
    public function inSubModuleContext(IModule $module, string $moduleRootPath) : ModuleContext
    {
        return new ModuleContext(
            $this->router,
            strpos($moduleRootPath, ':') !== false ? $moduleRootPath : $this->combineUrlPaths($this->rootUrl, $moduleRootPath),
            $this->titles,
            $this->breadcrumbs,
            $module->withoutRequiredPermissions(),
            true
        );
    }

    /**
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    protected function combineUrlPaths(string ... $paths) : string
    {
        $url = array_shift($paths);

        foreach ($paths as $path) {
            $url = rtrim($url, '/') . '/' . ltrim($path, '/');
        }

        return $url;
    }
}
