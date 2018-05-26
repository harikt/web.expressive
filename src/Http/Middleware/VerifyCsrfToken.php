<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Aura\Session\Session;
use ParagonIE\AntiCSRF\AntiCSRF;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Translation\Translator;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;

class VerifyCsrfToken implements MiddlewareInterface
{

    protected $csrf;

    protected $router;

    protected $session;

    protected $translator;

    public function __construct(AntiCSRF $csrf, RouterInterface $router, Session $session, Translator $translator)
    {
        $this->csrf = $csrf;
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ('XMLHttpRequest' != $request->getHeaderLine('X-Requested-With') && $request->getMethod() == "POST" && ! $this->csrf->validateRequest()) {
            $response = new Response('php://memory', 302);
            $server = $request->getServerParams();
            $segment = $this->session->getSegment('VerifyCsrfToken');
            $segment->setFlash('csrf_token', $this->translator->trans('csrf.token', [], 'dms'));
            return $response->withHeader('Location', $server['REQUEST_URI']);
        }

        return $handler->handle($request);
    }

}
