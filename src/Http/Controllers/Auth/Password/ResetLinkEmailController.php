<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth\Password;

use Dms\Core\Auth\IAuthSystem;
use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Auth\IAdmin;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Password\IPasswordResetService;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Auth\PasswordBroker;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The password reset controller
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResetLinkEmailController extends DmsController implements ServerMiddlewareInterface
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
     * @param IAuthSystem           $auth
     * @param PasswordBrokerManager $passwordBrokerManager
     * @param IPasswordResetService $passwordResetService
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        PasswordBrokerManager $passwordBrokerManager,
        IPasswordResetService $passwordResetService
    ) {
        parent::__construct($cms, $auth);

        // $this->middleware('dms.guest');
        $this->passwordBroker       = $passwordBrokerManager->broker('dms');
        $this->passwordResetService = $passwordResetService;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $this->loadSharedViewVariables($request);

        if ($request->getMethod() == "POST") {
            return $this->sendResetLinkEmail($request);
        }

        $this->showResetLinkEmailForm();
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Zend\Diactoros\Response
     */
    public function showResetLinkEmailForm()
    {
        return new HtmlResponse($this->template->render('dms::auth.password.forgot'));
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Zend\Diactoros\Response
     */
    public function sendResetLinkEmail(ServerRequestInterface $request)
    {
        // $this->validate($request, ['email' => 'required|email']);

        $credentials = ['emailAddress' => new EmailAddress($request->get('email'))];
        $response    = $this->passwordBroker->sendResetLink($credentials);

        switch ($response) {
            case PasswordBroker::RESET_LINK_SENT:
                return redirect()->back()->with('success', trans($response));

            case PasswordBroker::INVALID_USER:
                return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  string|null $token
     *
     * @return \Zend\Diactoros\Response
     */
    public function showPasswordResetForm(string $token = null)
    {
        if (!$token) {
            return $this->showResetLinkEmailForm();
        }

        return view('dms::auth.password.reset')->with('token', $token);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Zend\Diactoros\Response
     */
    public function reset(ServerRequestInterface $request)
    {
        $this->validate($request, [
            'token'    => 'required',
            'username' => 'required',
            'password' => 'required|confirmed|min:6|max:50',
        ]);

        $credentials = $request->only(
            'username',
            'password',
            'password_confirmation',
            'token'
        );

        $response = $this->passwordBroker->reset($credentials, function (IAdmin $user, $password) {
            $this->passwordResetService->resetUserPassword($user, $password);
        });

        switch ($response) {
            case PasswordBroker::PASSWORD_RESET:
                return redirect()->route('dms::auth.login')->with('success', trans($response));

            default:
                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);
        }
    }
}
