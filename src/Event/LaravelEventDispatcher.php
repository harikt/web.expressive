<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Event;

use Dms\Core\Event\EventDispatcher;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LaravelEventDispatcher extends EventDispatcher
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * LaravelEventDispatcher constructor.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, callable $listener)
    {
        $this->dispatcher->listen($event, $listener);
    }

    /**
     * @inheritDoc
     */
    public function once(string $event, callable $listener)
    {
        $this->dispatcher->listen(
            $event,
            function (... $arguments) use ($event, $listener) {
                $this->removeListener($event, $listener);
                return $listener(...$arguments);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function removeListener(string $event, callable $listener)
    {
        $listeners = $this->dispatcher->getListeners($event);

        $this->dispatcher->forget($event);

        foreach ($listeners as $otherListener) {
            if ($otherListener !== $listener) {
                $this->dispatcher->listen($event, $otherListener);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function removeAllListeners(string $event = null)
    {
        $this->dispatcher->forget($event ?? '*');
    }

    /**
     * @inheritDoc
     */
    public function getListeners(string $event) : array
    {
        return $this->dispatcher->getListeners($event);
    }

    /**
     * @inheritDoc
     */
    public function emit(string $event, ...$arguments)
    {
        return $this->dispatcher->fire($event, $arguments);
    }
}
