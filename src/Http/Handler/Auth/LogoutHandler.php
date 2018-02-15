<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler\Auth;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\NotAuthenticatedException;
use Dms\Core\ICms;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * The logout controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LogoutHandler implements RequestHandlerInterface
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
     * Logout controller instance.
     *
     * @param ICms $cms
     * @param IAuthSystem $auth
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
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->auth->logout();
        } catch (NotAuthenticatedException $e) {
        }
        $response = new Response();
        return $response->withHeader('Location', '/dms')->withStatus(302);
    }
}
