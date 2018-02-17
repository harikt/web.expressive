<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler\Auth\Password;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Auth\IAdmin;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Password\IPasswordResetService;
use Dms\Web\Expressive\Http\Handler\DmsHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The password reset controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResetLinkEmailHandler extends DmsHandler implements RequestHandlerInterface
{
    /**
     * @var IPasswordResetService
     */
    protected $passwordResetService;

    /**
     * Create a new password controller instance.
     *
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     * @param IPasswordResetService     $passwordResetService
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        IPasswordResetService $passwordResetService
    ) {
        parent::__construct($cms, $auth, $template, $router);

        $this->passwordResetService = $passwordResetService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() == "POST") {
            return $this->sendResetLinkEmail($request);
        }

        return $this->showResetLinkEmailForm();
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Zend\Diactoros\Response
     */
    public function showResetLinkEmailForm()
    {
        return new HtmlResponse($this->template->render('dms::auth.password.forgot', ['errors' => $this->errors]));
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Zend\Diactoros\Response
     */
    public function sendResetLinkEmail(ServerRequestInterface $request)
    {
        // $this->validate($request, ['email' => 'required|email']);
        $post = $request->getParsedBody();
        $email = $post['email'] ?: '';

        $credentials = ['emailAddress' => new EmailAddress($email)];
        // $response    = $this->passwordBroker->sendResetLink($credentials);

        $to = '';

        throw new \Exception("Not done");

        // todo change redirect method calls
        switch ($response) {
        case PasswordBroker::RESET_LINK_SENT:
            $response = new Response('php://memory', '302');
            $response->withHeader('Location', $to);
            return $response;
                // return redirect()->back()->with('success', trans($response));

        case PasswordBroker::INVALID_USER:
            $response = new Response('php://memory', '302');
            $response->withHeader('Location', $to);
            return $response;
                // return redirect()->back()->withErrors(['email' => trans($response)]);
        }
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
    public function showPasswordResetForm(string $token = null)
    {
        if (!$token) {
            return $this->showResetLinkEmailForm();
        }

        return $this->template->render('dms::auth.password.reset', ['token' => $token, 'errors' => $this->errors]);
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

        $credentials = $request->only(
            'username',
            'password',
            'password_confirmation',
            'token'
        );

        $response = $this->passwordBroker->reset(
            $credentials,
            function (IAdmin $user, $password) {
                $this->passwordResetService->resetUserPassword($user, $password);
            }
        );

        switch ($response) {
        case PasswordBroker::PASSWORD_RESET:
            $response = new Response();
            $response = $response->withHeader('Location', $this->router->generateUri('dms::auth.login'));

            // ->with('success', trans($response))
            return $response;

        default:
            $response = new Response();
            $response = $response->withHeader('Location', $this->router->generateUri('dms::auth.login'));

            return $response;
                // return redirect()->back()
                //     ->withInput($request->only('email'))
                //     ->withErrors(['email' => trans($response)]);
        }
    }
}
