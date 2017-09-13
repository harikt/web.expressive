<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Action;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Module\IAction;
use Dms\Core\Util\Debug;
use Dms\Web\Expressive\Http\ModuleContext;
use Zend\Diactoros\Response;

/**
 * The action result handler vase class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ActionResultHandler implements IActionResultHandler
{
    /**
     * @var string|null
     */
    protected $supportedResultType;

    /**
     * ActionResultHandler constructor.
     */
    public function __construct()
    {
        $this->supportedResultType = $this->supportedResultType();
    }

    /**
     * @return string|null
     */
    abstract protected function supportedResultType();

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return bool
     */
    abstract protected function canHandleResult(ModuleContext $moduleContext, IAction $action, $result) : bool;

    /**
     * @param ModuleContext $moduleContext
     * @param IAction       $action
     * @param mixed         $result
     *
     * @return Response|mixed
     */
    abstract protected function handleResult(ModuleContext $moduleContext, IAction $action, $result);

    /**
     * @inheritdoc
     */
    final public function getSupportedResultType()
    {
        return $this->supportedResultType;
    }

    /**
     * @inheritdoc
     */
    final public function accepts(ModuleContext $moduleContext, IAction $action, $result) : bool
    {
        if ($this->supportedResultType && !($result instanceof $this->supportedResultType)) {
            return false;
        }

        return $this->canHandleResult($moduleContext, $action, $result);
    }

    /**
     * @inheritdoc
     */
    final public function handle(ModuleContext $moduleContext, IAction $action, $result)
    {
        if (!$this->accepts($moduleContext, $action, $result)) {
            throw InvalidArgumentException::format(
                'Invalid call to %s: action and result of type %s not supported',
                get_class($this) . '::' . __FUNCTION__,
                Debug::getType($result)
            );
        }

        return $this->handleResult($moduleContext, $action, $result);
    }
}
