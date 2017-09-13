<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action\ExceptionHandler;

use Dms\Core\Auth\AdminForbiddenException;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Action\ActionExceptionHandler;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response\JsonResponse;

/**
 * The user forbidden exception handler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminForbiddenExceptionHandler extends ActionExceptionHandler
{
    /**
     * @return string|null
     */
    protected function supportedExceptionType()
    {
        return AdminForbiddenException::class;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return bool
     */
    protected function canHandleException(ModuleContext $moduleContext, IAction $action, \Exception $exception) : bool
    {
        return true;
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return JsonResponse|mixed
     */
    protected function handleException(ModuleContext $moduleContext, IAction $action, \Exception $exception)
    {
        return new JsonResponse([
            'message' => 'The current account is forbidden from running this action',
        ], 403);
    }
}
