<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Handler;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Illuminate\Support\MessageBag;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The base dms controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class DmsHandler
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
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var MessageBag
     */
    protected $errors;

    /**
     * DmsHandler constructor.
     *
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param TemplateRendererInterface $template
     * @param RouterInterface           $router
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router
    ) {
        $this->cms  = $cms;
        $this->auth = $auth;
        $this->template  = $template;
        $this->router = $router;
        $this->errors = new MessageBag();
    }

    protected function abort(ServerRequestInterface $request, int $statusCode, string $message = '')
    {
        $response = new Response('php://memory', $statusCode);
        if ('XMLHttpRequest' == $request->getHeaderLine('X-Requested-With')) {
            if ($statusCode == 401) {
                $message = json_encode(['redirect' => '/dms']);
            }
            $response->getBody()->write($message);
            return $response;
        }

        $response->getBody()->write($this->renderErrorView($statusCode));

        return $response;
    }

    /**
     * @param int $statusCode
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    protected function renderErrorView(int $statusCode)
    {
        return $this->template->render(
            'dms::errors.' . $statusCode,
            [
                'title' => $statusCode,
                'pageTitle' => $statusCode,
                'user'  => $this->auth->isAuthenticated() ? $this->auth->getAuthenticatedUser() : null,
                'finalBreadcrumb' => $statusCode,
            ]
        );
    }

}
