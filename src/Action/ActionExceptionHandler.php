<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IAction;
use Dms\Core\Util\Debug;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response;

/**
 * The action exception handler base class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ActionExceptionHandler implements IActionExceptionHandler
{
    /**
     * @var string|null
     */
    protected $supportedExceptionType;

    /**
     * ActionExceptionHandler constructor.
     */
    public function __construct()
    {
        $this->supportedExceptionType = $this->supportedExceptionType();
    }

    /**
     * @return string|null
     */
    abstract protected function supportedExceptionType();

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return bool
     */
    abstract protected function canHandleException(ModuleContext $moduleContext, IAction $action, \Exception $exception) : bool;

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param \Exception    $exception
     *
     * @return Response|mixed
     */
    abstract protected function handleException(ModuleContext $moduleContext, IAction $action, \Exception $exception);

    /**
     * @inheritdoc
     */
    final public function getSupportedExceptionType()
    {
        return $this->supportedExceptionType;
    }

    /**
     * @inheritdoc
     */
    final public function accepts(ModuleContext $moduleContext, IAction $action, \Exception $exception) : bool
    {
        if ($this->supportedExceptionType && !($exception instanceof $this->supportedExceptionType)) {
            return false;
        }

        return $this->canHandleException($moduleContext, $action, $exception);
    }

    /**
     * @inheritdoc
     */
    final public function handle(ModuleContext $moduleContext, IAction $action, \Exception $exception)
    {
        if (!$this->accepts($moduleContext, $action, $exception)) {
            throw InvalidArgumentException::format(
                'Invalid call to %s: action and exception of type not supported',
                get_class($this) . '::' . __FUNCTION__,
                Debug::getType($exception)
            );
        }

        return $this->handleException($moduleContext, $action, $exception);
    }
}
