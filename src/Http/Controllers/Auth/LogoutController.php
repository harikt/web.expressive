<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\NotAuthenticatedException;
use Dms\Core\ICms;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * The logout controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LogoutController implements ServerMiddlewareInterface
{
    /**
     * @var ICms
     */
    protected $cms;

    /**
     * @var IAuthSystem
     */
    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @param ICms $cms
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth
    ) {
        $this->cms  = $cms;
        $this->auth = $auth;
    }

    /**
     * Log the user out of the application.
     *
     * @return \Zend\Diactoros\Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate)
    {
        try {
            $this->auth->logout();
        } catch (NotAuthenticatedException $e) {
        }
        $response = new Response();
        return $response->withHeader('Location', '/');
    }
}
