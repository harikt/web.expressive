<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Dms\Core\Auth\IAuthSystem;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Session\SessionMiddleware;

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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        // set the session to auth system
        $this->auth->setSession($session);
        $path = '/dms'. $request->getUri()->getPath();
        $routeResult = $request->getAttribute(RouteResult::class);
        if (
            $this->auth->isAuthenticated() ||
            in_array($routeResult->getMatchedRouteName(), [
                'dms::auth.login',
                'dms::auth.password.forgot',
                'dms::auth.oauth.redirect',
                'dms::auth.oauth.response',
            ])
        ) {
            return $handler->handle($request);
        }

        $response = new Response();
        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            $message = json_encode(['redirect' => '/dms']);
            $response->getBody()->write($message);
            return $response->withStatus(401, 'Unauthenticated');
        } else {
            $response = $response->withHeader('location', $this->router->generateUri('dms::auth.login'));
            return $response->withStatus(302);
        }
    }
}
