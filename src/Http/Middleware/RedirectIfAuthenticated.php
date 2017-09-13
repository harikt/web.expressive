<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Closure;
use Dms\Core\Auth\IAuthSystem;

class RedirectIfAuthenticated
{
    /**
     * @var IAuthSystem
     */
    protected $auth;

    /**
     * Authenticate constructor.
     *
     * @param IAuthSystem $auth
     */
    public function __construct(IAuthSystem $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->isAuthenticated()) {
            return redirect()->route('dms::index');
        }

        return $next($request);
    }
}
