<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Dms\Core\Auth\IAuthSystem;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;

class Authenticate implements ServerMiddlewareInterface
{
    /**
     * @var IAuthSystem
     */
    protected $auth;

    protected $router;

    /**
     * Authenticate constructor.
     *
     * @param IAuthSystem $auth
     */
    public function __construct(
        IAuthSystem $auth,
        RouterInterface $router
    ) {
        $this->auth = $auth;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $path = '/dms'. $request->getUri()->getPath();

        if (
            $this->auth->isAuthenticated() ||
            $path == $this->router->generateUri('dms::auth.login') ||
            $path == $this->router->generateUri('dms::auth.password.forgot')
        ) {
            return $delegate->process($request);
        }

        $response = new Response();
        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            return $response->withStatus(401, 'Unauthenticated');
        } else {
            return $response->withHeader('Location', $this->router->generateUri('dms::auth.login'));
        }
    }
}
