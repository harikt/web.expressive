<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IAction;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response;

/**
 * The action exception handler collection class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ActionExceptionHandlerCollection
{
    /**
     * @var IActionExceptionHandler[][]
     */
    protected $handlers;

    /**
     * ActionExceptionHandlerCollection constructor.
     *
     * @param IActionExceptionHandler[] $handlers
     */
    public function __construct(array $handlers)
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'handlers', $handlers, IActionExceptionHandler::class);

        foreach ($handlers as $handler) {
            $this->handlers[$handler->getSupportedExceptionType()][] = $handler;
        }
    }

    /**
     * Handles the supplied action exception.
     *
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return Response|mixed
     * @throws UnhandleableActionExceptionException
     */
    public function handle(ModuleContext $moduleContext, IAction $action, \Exception $exception)
    {
        return $this->findHandlerFor($moduleContext, $action, $exception)->handle($moduleContext, $action, $exception);
    }

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return IActionExceptionHandler
     * @throws UnhandleableActionExceptionException
     */
    public function findHandlerFor(ModuleContext $moduleContext, IAction $action, \Exception $exception) : IActionExceptionHandler
    {
        $exceptionClass = get_class($exception);

        while ($exceptionClass) {
            if (isset($this->handlers[$exceptionClass])) {
                foreach ($this->handlers[$exceptionClass] as $exceptionHandler) {
                    if ($exceptionHandler->accepts($moduleContext, $action, $exception)) {
                        return $exceptionHandler;
                    }
                }
            }

            $exceptionClass = get_parent_class($exceptionClass);
        }

        throw new UnhandleableActionExceptionException(
            sprintf(
                'Could not handle action exception of type %s from action \'%s\': no matching action handler could be found',
                get_class($exception),
                $action->getName()
            ),
            0,
            $exception
        );
    }
}
