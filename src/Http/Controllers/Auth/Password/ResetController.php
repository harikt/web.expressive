<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth\Password;

use Dms\Core\Auth\IAdmin;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Password\IPasswordResetService;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Auth\PasswordBroker;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The password reset controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResetController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var PasswordBroker
     */
    protected $passwordBroker;

    /**
     * @var IPasswordResetService
     */
    protected $passwordResetService;

    /**
     * Create a new password controller instance.
     *
     * @param ICms                  $cms
     * @param PasswordBrokerManager $passwordBrokerManager
     * @param IPasswordResetService $passwordResetService
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        PasswordBrokerManager $passwordBrokerManager,
        IPasswordResetService $passwordResetService
    ) {
        parent::__construct($cms, $auth, $template, $router);

        $this->passwordBroker       = $passwordBrokerManager->broker('dms');
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param string|null $token
     *
     * @return \Zend\Diactoros\Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate)
    {
        $this->loadSharedViewVariables($request);
        $token = $request->getAttribute('token');

        if (!$token) {
            return new HtmlResponse($this->template->render('dms::auth.password.forgot'));
        }

        if ($request->getMethod() == "POST") {
            $this->reset($request);
        }

        return new HtmlResponse($this->template->render('dms::auth.password.reset', [
            'token' => $token,
        ]));
    }

    /**
     * Reset the given user's password.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Zend\Diactoros\Response
     */
    public function reset(ServerRequestInterface $request)
    {
        // $this->validate($request, [
        //     'token'    => 'required',
        //     'username' => 'required',
        //     'password' => 'required|confirmed|min:6|max:50',
        // ]);

        $post = $request->getParsedBody();
        $credentials = [
            'username' => isset($post['username']) ? $post['username'] : "",
            'password' => isset($post['password']) ? $post['password'] : "",
            'password_confirmation' => isset($post['password_confirmation']) ? $post['password_confirmation'] : "",
            'token' => isset($post['token']) ? $post['token'] : "",
        ];

        $response = $this->passwordBroker->reset($credentials, function (IAdmin $user, $password) {
            $this->passwordResetService->resetUserPassword($user, $password);
        });

        switch ($response) {
            case PasswordBroker::PASSWORD_RESET:
                // todo find equivalent of with, withInput, withErrors
                // with('success', trans($response));
                $response = new Response();
                $response = $response->withHeader('Location', $this->router->generateUri('dms::auth.login'));

                return $response;
            default:
                // return redirect()->back()
                //     ->withInput($request->only('email'))
                //     ->withErrors(['email' => trans($response)]);
                $referer = $request->getServerParams()['HTTP_REFERER'];
                $response = new Response();
                $response = $response->withHeader('Location', $referer);

                return $response;
        }
    }
}
