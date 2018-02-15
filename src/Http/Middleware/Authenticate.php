<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Dms\Core\Auth\IAuthSystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;

class Authenticate implements MiddlewareInterface
{
    /**
     * @var IAuthSystem
     */
    protected $auth;

    protected $router;

    /**
     * Authenticate constructor.
     *
     * @param IAuthSystem     $auth
     * @param RouterInterface $router
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
        $path = '/dms'. $request->getUri()->getPath();
        $routeResult = $request->getAttribute(RouteResult::class);
        if ($this->auth->isAuthenticated()
            || in_array(
                $routeResult->getMatchedRouteName(),

                [
                'dms::auth.login',
                'dms::auth.password.forgot',
                'dms::auth.oauth.redirect',
                'dms::auth.oauth.response',
                ]
            )
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
