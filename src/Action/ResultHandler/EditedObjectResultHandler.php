<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ResultHandler;

use Dms\Core\Common\Crud\Action\Crud\EditAction;
use Dms\Core\Common\Crud\IReadModule;
use Dms\Core\Model\Object\TypedObject;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionResultHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Util\StringHumanizer;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The edited object action result handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EditedObjectResultHandler extends ActionResultHandler
{
    /**
     * @return string|null
     */
    protected function supportedResultType()
    {
        return TypedObject::class;
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
        return $moduleContext->getModule() instanceof IReadModule && $action instanceof EditAction;
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
 * @var IReadModule $module
*/
        $module = $moduleContext->getModule();
        $label  = $module->getLabelFor($result);
        $type   = str_singular(StringHumanizer::humanize($module->getName()));

        return new JsonResponse(
            [
            'message' => "The '{$label}' {$type} has been updated.",
            ]
        );
    }
}
