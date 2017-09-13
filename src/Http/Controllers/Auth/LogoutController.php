<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth;

use Dms\Core\Auth\AdminBannedException;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\InvalidCredentialsException;
use Dms\Core\Auth\NotAuthenticatedException;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Oauth\OauthProviderCollection;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Illuminate\Cache\RateLimiter;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

/**
 * The login controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LogoutController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var OauthProviderCollection
     */
    protected $oauthProviderCollection;

    /**
     * Create a new authentication controller instance.
     *
     * @param ICms $cms
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        OauthProviderCollection $oauthProviderCollection
    ) {
        parent::__construct($cms, $auth);

        // $this->middleware('dms.guest', ['except' => 'logout']);
        $this->oauthProviderCollection = $oauthProviderCollection;
    }

    /**
     * Log the user out of the application.
     *
     * @return \Zend\Diactoros\Response
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        try {
            $this->auth->logout();
        } catch (NotAuthenticatedException $e) {
        }
        $response = new Response();
        return $response->withHeader('Location', '/');
    }
}
