<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler\Auth;

use Aura\Session\Session;
use BehEh\Flaps\Flap;
use Dms\Core\Auth\AdminBannedException;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\InvalidCredentialsException;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Oauth\OauthProviderCollection;
use Dms\Web\Expressive\Exception\TooManyFailedAttemptsException;
use Dms\Web\Expressive\Exception\ValidationFailedException;
use Dms\Web\Expressive\Http\Handler\DmsHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Translation\Translator;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * The login controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LoginHandler extends DmsHandler implements RequestHandlerInterface
{
    /**
     * @var OauthProviderCollection
     */
    protected $oauthProviderCollection;

    protected $flap;

    protected $session;

    protected $translator;

    /**
     * Create a new authentication controller instance.
     *
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     * @param OauthProviderCollection   $oauthProviderCollection
     * @param Flap                      $flap
     * @param Translator                $translator
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        OauthProviderCollection $oauthProviderCollection,
        Flap $flap,
        Translator $translator,
        Session $session
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->oauthProviderCollection = $oauthProviderCollection;
        $this->flap = $flap;
        $this->translator = $translator;
        $this->session = $session;
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() == "POST") {
            try {
                $username = new Input('username');
                $username->setRequired(true);

                $password = new Input('password');
                $password->setRequired(true);

                $inputFilter = new InputFilter();
                $inputFilter->add($username);
                $inputFilter->add($password);
                $inputFilter->setData($request->getParsedBody());

                if (! $this->flap->limit($this->getThrottleKey($request))) {
                    // too many login attempts
                    throw TooManyFailedAttemptsException::defaultMessage();
                }

                if ($inputFilter->isValid()) {
                    $this->auth->login($request->getParsedBody()['username'], $request->getParsedBody()['password']);

                    if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
                        return new JsonResponse(
                            [
                                'response'   => 'Authenticated',
                                'csrf_token' => js_csrf_token(),
                            ]
                        );
                    } else {
                        $to = $this->router->generateUri('dms::index');
                        $response = new Response('php://memory', 302);
                        return $response->withHeader('Location', $to);
                    }
                } else {
                    foreach ($inputFilter->getInvalidInput() as $key => $error) {
                        foreach ($error->getMessages() as $message) {
                            $this->errors->add($key, $message);
                        }
                    }
                }

            } catch (InvalidCredentialsException $e) {
                $this->errors->add('username', $this->translator->trans('auth.failed', [], 'dms'));
            } catch (AdminBannedException $e) {
                $this->errors->add('username', $this->translator->trans('auth.banned', [], 'dms'));
            } catch (TooManyFailedAttemptsException $e) {
                $this->errors->add('username', $this->translator->trans('auth.throttle', ['%seconds%' => $seconds], 'dms'));
            } catch (ValidationFailedException $e) {
            }

            if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
                $response = new Response('php://memory', 400);
                $response->getBody()->write('Failed');

                return $response;
            }
        }

        $segment = $this->session->getSegment('VerifyCsrfToken');
        $this->errors->add('csrf_token', $segment->getFlash('csrf_token'));

        return new HtmlResponse(
            $this->template->render(
                'dms::auth/login',
                [
                    'oauthProviders' => $this->oauthProviderCollection->getAll(),
                    'errors' => $this->errors,
                ]
            )
        );
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
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
