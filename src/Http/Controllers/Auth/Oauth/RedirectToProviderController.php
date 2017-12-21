<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth\Oauth;

use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\IRoleRepository;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Oauth\OauthProviderCollection;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

// todo
/**
 * The oauth login controller.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RedirectToProviderController extends DmsController implements ServerMiddlewareInterface
{
    /**
     * @var OauthProviderCollection
     */
    protected $providerCollection;

    /**
     * @var IAdminRepository
     */
    protected $adminRepository;

    /**
     * @var IRoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new oauth controller instance.
     *
     * @param ICms                    $cms
     * @param OauthProviderCollection $providerCollection
     * @param IAdminRepository        $adminRepository
     * @param IRoleRepository         $roleRepository
     */
    public function __construct(
        ICms $cms,
        IAuthSystem $auth,
        TemplateRendererInterface $template,
        RouterInterface $router,
        OauthProviderCollection $providerCollection,
        IAdminRepository $adminRepository,
        IRoleRepository $roleRepository
    ) {
        parent::__construct($cms, $auth, $template, $router);

        $this->providerCollection = $providerCollection;
        $this->adminRepository    = $adminRepository;
        $this->roleRepository     = $roleRepository;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate)
    {
        $providerName = $request->getAttribute('provider');

        if (! $this->providerCollection->has($providerName)) {
            return $this->abort($request, 401);
        }

        $oauthProvider = $this->providerCollection->getAll()[$providerName];

        $url = $oauthProvider->getProvider()->getAuthorizationUrl();

        // todo keep in session
        // $request->session()->put('dms-oauth-state', $oauthProvider->getProvider()->getState());

        $response = new Response();
        $response->withHeader('Location', $url);

        return $response;
    }
}
