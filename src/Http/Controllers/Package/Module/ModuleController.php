<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Renderer\Module\ModuleRendererCollection;
use Dms\Web\Expressive\Util\StringHumanizer;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface; 
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The module controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ModuleController extends DmsController implements ServerMiddlewareInterface
{
    use ModuleContextTrait;

    /**
     * @var ModuleRendererCollection
     */
    protected $moduleRenderers;

    /**
     * ModuleController constructor.
     *
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param ModuleRendererCollection  $moduleRenderers
     * @param TemplateRendererInterface $template
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        ModuleRendererCollection $moduleRenderers
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->moduleRenderers = $moduleRenderers;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface      $handler
     *
     * @return \Zend\Diactoros\Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $moduleContext = $this->getModuleContext($request, $this->router, $this->cms);

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
