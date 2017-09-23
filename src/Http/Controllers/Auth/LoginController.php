<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth;

use BehEh\Flaps\Flap;
use Dms\Core\Auth\AdminBannedException;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\InvalidCredentialsException;
use Dms\Core\Auth\NotAuthenticatedException;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Oauth\OauthProviderCollection;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The login controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LoginController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var OauthProviderCollection
     */
    protected $oauthProviderCollection;

    protected $flap;

    /**
     * Create a new authentication controller instance.
     *
     * @param ICms $cms
     * @param IAuthSystem $auth
     * @param OauthProviderCollection $oauthProviderCollection
     * @param TemplateRendererInterface $template
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        OauthProviderCollection $oauthProviderCollection,
        Flap $flap
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->oauthProviderCollection = $oauthProviderCollection;
        $this->flap = $flap;
    }


    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $this->loadSharedViewVariables($request);

        if ($request->getMethod() == "POST") {
            return $this->login($request);
        }

        return new HtmlResponse($this->template->render('dms::auth/login', ['oauthProviders' => $this->oauthProviderCollection->getAll()]));
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return Response
     */
    public function login(ServerRequestInterface $request)
    {
        // $this->validate($request, [
        //     'username' => 'required',
        //     'password' => 'required',
        // ]);

        if (! $this->flap->limit($this->getThrottleKey($request))) {
            $response = new Response();

            // @todo
            $response->getBody()->write("Failed!");

            return $response;
        }

        try {
            $username = $request->getParsedBody()['username'];
            $password = $request->getParsedBody()['password'];
            $this->auth->login($username, $password);

            if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
                return new JsonResponse([
                    'response'   => 'Authenticated',
                    'csrf_token' => csrf_token(),
                ]);
            } else {
                $to = $this->router->generateUri('dms::index');
                $response = new Response('php://memory', 302);
                return $response->withHeader('Location', $to);
            }
        } catch (InvalidCredentialsException $e) {
            $errorMessage = 'dms::auth.failed';
        } catch (AdminBannedException $e) {
            $errorMessage = 'dms::auth.banned';
        }

        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            return response('Failed', 400);
        } else {
            $to = $this->router->generateUri('dms::auth.login');
            $response = new Response('php://memory', 302);

            return $response->withHeader('Location', $to);
        }
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return string
     */
    protected function getThrottleKey(ServerRequestInterface $request) : string
    {
        $ipAddress = $request->getAttribute('ip_address');
        if (! $ipAddress) {
            $ipAddress = $request->getServerParams()['REMOTE_ADDR'];
        }

        return strtolower($request->getParsedBody()['username']) . '|' . $ipAddress;
    }
}
