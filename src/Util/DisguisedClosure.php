<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Util;

/**
 * This is useful for disguising closures so they do not get implicitly
 * invoked by laravel's view engine.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DisguisedClosure
{
    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * WrappedClosure constructor.
     *
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function __invoke(...$arguments)
    {
        return call_user_func_array($this->closure, $arguments);
    }
}
