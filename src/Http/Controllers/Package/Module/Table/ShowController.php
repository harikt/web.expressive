<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Package\Module\Table;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Common\Crud\Table\ISummaryTable;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\ICms;
use Dms\Core\Module\IModule;
use Dms\Core\Module\ITableDisplay;
use Dms\Core\Module\ITableView;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Http\Controllers\Package\Module\ModuleContextTrait;
use Dms\Web\Expressive\Renderer\Table\TableRenderer;
use Dms\Web\Expressive\Util\StringHumanizer;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface; 
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The table controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ShowController extends DmsController implements ServerMiddlewareInterface
{
    use ModuleContextTrait;

    /**
     * @var TableRenderer
     */
    protected $tableRenderer;

    /**
     * TableController constructor.
     *
     * @param ICms          $cms
     * @param TableRenderer $tableRenderer
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        TableRenderer $tableRenderer
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->tableRenderer = $tableRenderer;
        ;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $urlHelper = new UrlHelper($this->router);
        $tableName = $request->getAttribute('table');
        $viewName = $request->getAttribute('view');

        $moduleContext = $this->getModuleContext($request, $this->router, $this->cms);

        $module = $moduleContext->getModule();

        $table = $this->loadTable($module, $tableName);

        if ($table instanceof ISummaryTable) {
            $to = $urlHelper->generate('dms::package.module.dashboard', [
                'package' => $package->getName(),
                'module'  => $firstModule,
            ], [
                '__content_only' => isset($request->getQueryParams()['__content_only']) ? $request->getQueryParams()['__content_only'] : null,
                '__template_only' => isset($request->getQueryParams()['__template_only']) ? $request->getQueryParams()['__template_only'] : null,
                '__no_template' => isset($request->getQueryParams()['__no_template']) ? $request->getQueryParams()['__no_template'] : null,
            ]);

            $response = new Response('php://memory', 302);
            return $response->withHeader('Location', $to);
            // return redirect()
            //     ->to($moduleContext->getUrl('dashboard', array_filter(request()->only('__content_only', '__no_template'))))
            //     ->with('initial-view-name', $viewName);
        }

        $this->loadTableView($table, $viewName);

        return new HtmlResponse(
            $this->template->render('dms::package.module.table', [
                'assetGroups'     => ['tables'],
                'pageTitle'       => implode(' :: ', array_merge($moduleContext->getTitles(), [StringHumanizer::title($tableName)])),
                'pageSubTitle'    => $viewName,
                'breadcrumbs'     => $moduleContext->getBreadcrumbs(),
                'finalBreadcrumb' => StringHumanizer::title($tableName),
                'tableContent'    => $this->tableRenderer->renderTableControl($moduleContext, $table, $viewName),
            ])
        );
    }

    /**
     * @param ITableDisplay $table
     * @param string        $viewName
     *
     * @return ITableView
     */
    protected function loadTableView(ITableDisplay $table, string $viewName) : ITableView
    {
        try {
            return $table->getView($viewName);
        } catch (InvalidArgumentException $e) {
            return $this->abort($request, 404);
        }
    }

    /**
     * @param IModule $module
     * @param string  $tableName
     *
     * @return array|ITableDisplay
     */
    protected function loadTable(IModule $module, string $tableName) : ITableDisplay
    {
        try {
            return $module->getTable($tableName);
        } catch (InvalidArgumentException $e) {
            $response = new JsonResponse([
                'message' => 'Invalid table name',
            ], 404);
        }

        return $response;
    }
}
