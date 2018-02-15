<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler\Package\Module;

use Dms\Core\ICms;
use Dms\Web\Expressive\Http\ModuleContext;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;

trait ModuleContextTrait
{
    public function getModuleContext(ServerRequestInterface $request, RouterInterface $router, ICms $cms)
    {
        $packageName = $request->getAttribute('package');
        $moduleName = $request->getAttribute('module');

        $moduleContext = ModuleContext::rootContext(
            $router,
            $packageName,
            $moduleName,
            function () use ($packageName, $moduleName, $cms) {
                $package = $cms->loadPackage($packageName);

                return $package->loadModule($moduleName);
            }
        );

        return $moduleContext;
    }
}
