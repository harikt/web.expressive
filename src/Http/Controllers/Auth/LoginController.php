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
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
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

    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    protected $urlHelper;

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
        OauthProviderCollection $oauthProviderCollection,
        TemplateRendererInterface $template,
        UrlHelper $urlHelper
    ) {
        parent::__construct($cms, $auth);

        $this->template = $template;
        $this->urlHelper = $urlHelper;

        // $this->middleware('dms.guest', ['except' => 'logout']);
        $this->oauthProviderCollection = $oauthProviderCollection;
    }

    /**
     * Get the maximum number of login attempts for delaying further attempts.
     *
     * @return int
     */
    protected function maxLoginAttempts() : int
    {
        return config('dms.auth.login.max-attempts');
    }

    /**
     * The number of seconds to delay further login attempts.
     *
     * @return int
     */
    protected function lockoutTime() : int
    {
        return config('dms.auth.login.lockout-time');
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
     * Show the application login form.
     *
     * @return \Zend\Diactoros\Response
     */
    public function showLoginForm()
    {
        return view('dms::auth.login', ['oauthProviders' => $this->oauthProviderCollection->getAll()]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Zend\Diactoros\Response
     */
    public function login(ServerRequestInterface $request)
    {
        // $this->validate($request, [
        //     'username' => 'required',
        //     'password' => 'required',
        // ]);

        // if ($this->hasTooManyLoginAttempts($request)) {
        //     return $this->sendLockoutResponse($request);
        // }

        try {
            $username = $request->getParsedBody()['username'];
            $password = $request->getParsedBody()['password'];
            $this->auth->login($username, $password);

            // $this->clearLoginAttempts($request);

            if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
                return new JsonResponse([
                    'response'   => 'Authenticated',
                    'csrf_token' => csrf_token(),
                ]);
            } else {
                $to = $this->urlHelper->generate('dms::index');
                $response = new Response('php://memory', 302);
                return $response->withHeader('Location', $to);
                // return redirect()->intended(route('dms::index'));
            }
        } catch (InvalidCredentialsException $e) {
            $errorMessage = 'dms::auth.failed';
        } catch (AdminBannedException $e) {
            $errorMessage = 'dms::auth.banned';
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        // $this->incrementLoginAttempts($request);

        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            return response('Failed', 400);
        } else {
            // @todo
            $to = '/dms/auth/login';
            $response = new Response('php://memory', 302);
            return $response->withHeader('Location', $to);
            // return redirect()->back()
            //     ->withInput($request->only('username'))
            //     ->withErrors([
            //         'username' => trans($errorMessage),
            //     ]);
        }
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return bool
     */
    protected function hasTooManyLoginAttempts(ServerRequestInterface $request) : bool
    {
        return app(RateLimiter::class)->tooManyAttempts(
            $this->getThrottleKey($request),
            $this->maxLoginAttempts(),
            $this->lockoutTime() / 60
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return void
     */
    protected function incrementLoginAttempts(ServerRequestInterface $request)
    {
        app(RateLimiter::class)->hit(
            $this->getThrottleKey($request)
        );
    }

    /**
     * Determine how many retries are left for the user.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return int
     */
    protected function retriesLeft(ServerRequestInterface $request) : int
    {
        $attempts = app(RateLimiter::class)->attempts(
            $this->getThrottleKey($request)
        );

        return $this->maxLoginAttempts() - $attempts + 1;
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(ServerRequestInterface $request)
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $this->getThrottleKey($request)
        );

        return redirect()->back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors([
                'username' => trans('dms::auth.throttle', ['seconds' => $seconds]),
            ]);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return void
     */
    protected function clearLoginAttempts(ServerRequestInterface $request)
    {
        app(RateLimiter::class)->clear(
            $this->getThrottleKey($request)
        );
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
        return mb_strtolower($request->input('username')) . '|' . $request->ip();
    }
}
