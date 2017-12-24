<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Middleware;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface; 
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LoadVariablesToTemplate implements ServerMiddlewareInterface
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
     * 
     * @param ICms                      $cms
     * @param IAuthSystem               $auth
     * @param TemplateRendererInterface $template
     *
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template
    ) {
        $this->cms  = $cms;
        $this->auth = $auth;
        $this->template = $template;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = [
            'cms'   => $this->cms,
            'user'  => $this->auth->isAuthenticated() ? $this->auth->getAuthenticatedUser() : null,
            'title' => 'DMS {' . $request->getServerParams()['SERVER_NAME'] . '}',
            'requestUri' => $request->getUri()->__toString(),
            '__content_only' => isset($request->getQueryParams()['__content_only']) ? $request->getQueryParams()['__content_only'] : null,
            '__template_only' => isset($request->getQueryParams()['__template_only']) ? $request->getQueryParams()['__template_only'] : null,
            '__no_template' => isset($request->getQueryParams()['__no_template']) ? $request->getQueryParams()['__no_template'] : null,
        ];
        foreach ($params as $param => $value) {
            $this->template->addDefaultParam(
                TemplateRendererInterface::TEMPLATE_ALL,
                $param,
                $value
            );
        }

        return $handler->handle($request);
    }
}
