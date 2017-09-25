<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Core\Package\IPackage;
use Dms\Web\Expressive\Error\DmsError;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Renderer\Package\PackageRendererCollection;
use Dms\Web\Expressive\Util\StringHumanizer;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * The packages controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PackageController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var IPackage
     */
    protected $package;

    /**
     * @var PackageRendererCollection
     */
    protected $packageRenderers;

    /**
     * PackageController constructor.
     *
     * @param ICms                      $cms
     * @param PackageRendererCollection $packageRenderers
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        PackageRendererCollection $packageRenderers
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->packageRenderers = $packageRenderers;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $result = $this->loadPackage($request);

        if ($result instanceof ResponseInterface) {
            // todo move as middleware ?
            return $result;
        }

        if (!$this->package->hasDashboard() || !$this->package->loadDashboard()->getAuthorizedWidgets()) {
            $moduleNames = $this->package->getModuleNames();
            $firstModule = reset($moduleNames);
            $urlHelper = new UrlHelper($this->router);
            $to = $urlHelper->generate('dms::package.module.dashboard', [
                'package' => $this->package->getName(),
                'module'  => $firstModule,
            ], [
                '__no_template' => isset($request->getQueryParams()['__no_template']) ? $request->getQueryParams()['__no_template'] : '',
                '__content_only' => isset($request->getQueryParams()['__content_only']) ? $request->getQueryParams()['__content_only'] : '',
            ]);

            $response = new Response('php://memory', 302);
            return $response->withHeader('Location', $to);
        }

        $packageName = $this->package->getName();

        $this->loadSharedViewVariables($request);

        return new HtmlResponse(
            $this->template->render(
                'dms::package.dashboard',
                [
                    'assetGroups'      => ['tables', 'charts', 'forms'],
                    'pageTitle'        => StringHumanizer::title($packageName) . ' :: Dashboard',
                    'breadcrumbs'      => [
                        $this->router->generateUri('dms::index') => 'Home',
                    ],
                    'finalBreadcrumb'  => StringHumanizer::title($packageName),
                    'packageRenderers' => $this->packageRenderers,
                    'package'          => $this->package,
                ]
            )
        );
    }

    protected function loadPackage(ServerRequestInterface $request)
    {
        $packageName = $request->getAttribute('package');

        if (!$this->cms->hasPackage($packageName)) {
            return DmsError::abort($request, 404, 'Unrecognized package name');
        }

        $this->package = $this->cms->loadPackage($packageName);
    }
}
