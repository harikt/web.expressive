<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Dms\Core\Auth\IAuthSystem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;

class RedirectIfAuthenticated implements MiddlewareInterface
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
