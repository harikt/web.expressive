<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ResultHandler;

use Dms\Common\Structure\Web\Html;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionResultHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The html action result handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class HtmlResultHandler extends ActionResultHandler
{
    /**
     * @return string|null
     */
    protected function supportedResultType()
    {
        return Html::class;
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
        return true;
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
        /**
         * @var Html $result
        */

        return new JsonResponse(
            [
            'content'       => $result->asString(),
            'iframe'        => true,
            ]
        );
    }
}
