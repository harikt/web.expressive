<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Module\ModuleRendererCollection;
use Dms\Web\Expressive\Util\StringHumanizer;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * The module controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ModuleController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * @var ModuleRendererCollection
     */
    protected $moduleRenderers;

    protected $router;

    /**
     * ModuleController constructor.
     *
     * @param ICms                      $cms
     * @param IAuthSystem 			    $auth
     * @param ModuleRendererCollection  $moduleRenderers
     * @param TemplateRendererInterface $template
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        ModuleRendererCollection $moduleRenderers,
        TemplateRendererInterface $template,
        RouterInterface $router
    ) {
        parent::__construct($cms, $auth);
        $this->moduleRenderers = $moduleRenderers;
        // $this->moduleContext = $moduleContext;
        $this->template = $template;
        $this->router = $router;
    }

    /**
     * @param ModuleContext $moduleContext
     *
     * @return mixed
     */
    //  public function showDashboard(ModuleContext $moduleContext)
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $packageName = $request->getAttribute('package');
        $moduleName = $request->getAttribute('module');
        $package = $this->cms->loadPackage($packageName);
        $moduleContext = ModuleContext::rootContext($this->router, $packageName, $moduleName, function () use ($package, $moduleName) {
            return $package->loadModule($moduleName);
        });
        $module = $moduleContext->getModule();

        $this->loadSharedViewVariables($request);

        return new HtmlResponse(
            $this->template->render(
                'dms::package.module.dashboard',
                [
                    'assetGroups'     => ['tables', 'charts'],
                    'pageTitle'       => implode(' :: ', $moduleContext->getTitles()),
                    'breadcrumbs'     => array_slice($moduleContext->getBreadcrumbs(), 0, -1, true),
                    'finalBreadcrumb' => StringHumanizer::title($moduleContext->getModule()->getName()),
                    'moduleContent'   => $this->moduleRenderers->findRendererFor($moduleContext)->render($moduleContext),
                ]
            )
        );
    }
}
