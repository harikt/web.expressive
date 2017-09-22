<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth\Oauth;

use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\IAuthSystem;
use Dms\Core\Auth\IRoleRepository;
use Dms\Core\Auth\Permission;
use Dms\Core\ICms;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\Oauth\AdminAccountDetails;
use Dms\Web\Expressive\Auth\Oauth\OauthProvider;
use Dms\Web\Expressive\Auth\Oauth\OauthProviderCollection;
use Dms\Web\Expressive\Auth\OauthAdmin;
use Dms\Web\Expressive\Auth\Role;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Dms\Web\Expressive\Http\Controllers\DmsController;
use Illuminate\Http\Exceptions\HttpResponseException;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

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
        OauthProviderCollection $providerCollection,
        IAdminRepository $adminRepository,
        IRoleRepository $roleRepository
    ) {
        parent::__construct($cms, $auth);

        // $this->middleware('dms.guest');
        $this->providerCollection = $providerCollection;
        $this->adminRepository    = $adminRepository;
        $this->roleRepository     = $roleRepository;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $providerName = $request->getAttribute('provider');

        $oauthProvider = $this->getProvider($providerName);

        $url = $oauthProvider->getProvider()->getAuthorizationUrl();

        $request->session()->put('dms-oauth-state', $oauthProvider->getProvider()->getState());

        return \redirect($url);
    }

    private function getProvider(string $providerName) : OauthProvider
    {
        abort_unless($this->providerCollection->has($providerName), 404);

        return $this->providerCollection->getAll()[$providerName];
    }
}
