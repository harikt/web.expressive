<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response;

/**
 * The action result handler collection class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionResultHandlerCollection
{
    /**
     * @var IActionResultHandler[][]
     */
    protected $handlers;

    /**
     * ActionResultHandlerCollection constructor.
     *
     * @param IActionResultHandler[] $handlers
     */
    public function __construct(array $handlers)
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'handlers', $handlers, IActionResultHandler::class);

        foreach ($handlers as $handler) {
            $this->handlers[$handler->getSupportedResultType() ?? '<any>'][] = $handler;
        }
    }

    /**
     * Handles the supplied action result.
     *
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return Response|mixed
     * @throws UnhandleableActionResultException
     */
    public function handle(ModuleContext $moduleContext, IAction $action, $result)
    {
        return $this->findHandlerFor($moduleContext, $action, $result)->handle($moduleContext, $action, $result);
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return IActionResultHandler
     * @throws UnhandleableActionResultException
     */
    public function findHandlerFor(ModuleContext $moduleContext, IAction $action, $result) : IActionResultHandler
    {
        $resultClass = get_class($result);

        while ($resultClass) {
            if (isset($this->handlers[$resultClass])) {
                foreach ($this->handlers[$resultClass] as $resultHandler) {
                    if ($resultHandler->accepts($moduleContext, $action, $result)) {
                        return $resultHandler;
                    }
                }
            }

            $resultClass = get_parent_class($resultClass);
        }

        foreach ($this->handlers['<any>'] as $resultHandler) {
            if ($resultHandler->accepts($moduleContext, $action, $result)) {
                return $resultHandler;
            }
        }

        throw UnhandleableActionResultException::format(
            'Could not handle action result of type %s from action \'%s\': no matching action handler could be found',
            get_class($result),
            $action->getName()
        );
    }
}
