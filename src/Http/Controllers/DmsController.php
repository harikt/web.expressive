<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers;

use Dms\Core\Auth\IAuthSystem;
use Dms\Core\ICms;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The base dms controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DmsController
{
    // use ValidatesRequests;

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
     * DmsController constructor.
     *
     * @param ICms $cms
     * @param IAuthSystem $auth
     * @param ICms $cms
     * @param IAuthSystem $auth
     *
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
    }

    /**
     * @param ServerRequestInterface $request
     */
    protected function loadSharedViewVariables(ServerRequestInterface $request)
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
            $this->template->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL,
                $param,
                $value
            );
        }
    }
}
