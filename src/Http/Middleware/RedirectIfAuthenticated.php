<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Dms\Core\Auth\IAuthSystem;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;

class RedirectIfAuthenticated implements ServerMiddlewareInterface
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
     * @param RouterInterface $router
     */
    public function __construct(IAuthSystem $auth, RouterInterface $router)
    {
        $this->auth = $auth;
        $this->router = $router;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->auth->isAuthenticated()) {
            $response = new Response();
            $response = $response->withHeader('Location', $this->router->generateUri('dms::index'));

            return $response;
        }

        return $handler($request);
    }
}
