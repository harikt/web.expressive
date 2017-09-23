<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Renderer\Package\PackageRendererCollection;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The root controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IndexController extends DmsController implements ServerMiddlewareInterface
{
    private $dashboardRenderer;

    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        PackageRendererCollection $dashboardRenderer
    ) {
        parent::__construct($cms, $auth, $template, $router);

        $this->dashboardRenderer = $dashboardRenderer;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if ($this->cms->hasPackage('analytics')) {
            $package           = $this->cms->loadPackage('analytics');
            $analyticsWidgets = $this->dashboardRenderer->findRendererFor($package)->render($package);
        } else {
            $analyticsWidgets = null;
        }

        $this->loadSharedViewVariables($request);

        return new HtmlResponse($this->template->render('dms::dashboard', [
            'assetGroups'      => ['tables', 'charts'],
            'pageTitle'        => 'Dashboard',
            'finalBreadcrumb'  => 'Dashboard',
            'analyticsWidgets' => $analyticsWidgets,
            'request'           => $request,
        ]));
    }
}
