<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth;

use BehEh\Flaps\Flap;
use Dms\Core\Auth\AdminBannedException;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\InvalidCredentialsException;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Oauth\OauthProviderCollection;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Dms\Web\Expressive\Exception\TooManyFailedAttemptsException;
use Dms\Web\Expressive\Exception\ValidationFailedException;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface; 
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints as Assert;
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

    protected $translator;

    /**
     * Create a new authentication controller instance.
     *
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param OauthProviderCollection   $oauthProviderCollection
     * @param TemplateRendererInterface $template
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        OauthProviderCollection $oauthProviderCollection,
        Flap $flap,
        Translator $translator
    ) {
        parent::__construct($cms, $auth, $template, $router);
        $this->oauthProviderCollection = $oauthProviderCollection;
        $this->flap = $flap;
        $this->translator = $translator;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() == "POST") {

            $constraint = new Assert\Collection([
                'username' => [
                    new Assert\NotBlank(),
                ],
                'password' => [
                    new Assert\NotBlank(),
                ],
                '_token' => []
            ]);

            try {
                $this->validate($request->getParsedBody(), $constraint);

                if (! $this->flap->limit($this->getThrottleKey($request))) {
                    // too many login attempts                    
                    throw TooManyFailedAttemptsException::defaultMessage();
                }
                
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
