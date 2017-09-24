<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ResultHandler;

use Dms\Core\Common\Crud\IReadModule;
use Dms\Core\Model\EntityCollection;
use Dms\Core\Module\IAction;
use Dms\Core\Table\DataSource\ObjectTableDataSource;
use Dms\Web\Expressive\Action\ActionResultHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Table\TableRenderer;
use Dms\Web\Expressive\Util\EntityModuleMap;
use Dms\Web\Expressive\Util\StringHumanizer;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The generic entity collection action result handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GenericEntityCollectionResultHandler extends ActionResultHandler
{
    /**
     * @var EntityModuleMap|null
     */
    protected $entityModuleMap;

    protected function getEntityModuleMap() : EntityModuleMap
    {
        if (!$this->entityModuleMap) {
            $this->entityModuleMap = app(EntityModuleMap::class);
        }

        return $this->entityModuleMap;
    }

    /**
     * @return string|null
     */
    protected function supportedResultType()
    {
        return EntityCollection::class;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return bool
     */
    protected function canHandleResult(ModuleContext $moduleContext, IAction $action, $result) : bool
    {
        /** @var EntityCollection $result */

        return $this->getEntityModuleMap()->loadModuleFor($result->getObjectType()) instanceof IReadModule;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return Response|mixed
     */
    protected function handleResult(ModuleContext $moduleContext, IAction $action, $result)
    {
        /** @var EntityCollection $result */
        /** @var IReadModule $module */
        $module = $this->getEntityModuleMap()->loadModuleFor($result->getObjectType());

        /** @var ObjectTableDataSource $tableDataSource */
        $tableDataSource = $module->getSummaryTable()->getDataSource();

        /** @var TableRenderer $tableRenderer */
        $tableRenderer = app(TableRenderer::class);

        $tableHtml = $tableRenderer->renderTableData(
            ModuleContext::rootContextForModule($moduleContext->getRouter(), $module),
            $module->getSummaryTable(),
            $tableDataSource->loadFromObjects($result->asArray()),
            null,
            true
        );

        return new JsonResponse([
            'content_title' => StringHumanizer::title($module->getName()),
            'content'       => $tableHtml,
        ]);
    }
}
