<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response;

/**
 * The action result handler interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IActionResultHandler
{
    /**
     * Gets the class name string for which this result handler can process
     * or null if no specific class is supported.
     *
     * @return string|null
     */
    public function getSupportedResultType();

    /**
     * Returns whether the result handler can handle the supplied result from
     * the supplied action.
     *
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return bool
     */
    public function accepts(ModuleContext $moduleContext, IAction $action, $result) : bool;

    /**
     * Handles the supplied action result and returns the appropriate HTTP response for handling
     * the result.
     *
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return Response|mixed
     */
    public function handle(ModuleContext $moduleContext, IAction $action, $result);
}
