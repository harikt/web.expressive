<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Http\Controllers\Auth\Oauth;

use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Auth\IRoleRepository;
use Dms\Core\Auth\Permission;
use Dms\Core\Auth\IAuthSystem;
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
class HandleProviderResponseController extends DmsController implements ServerMiddlewareInterface
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
        $this->cms                = $cms;
        $this->adminRepository    = $adminRepository;
        $this->roleRepository     = $roleRepository;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $providerName = $request->getAttribute('provider');

        $oauthProvider = $this->getProvider($providerName);

        $state = $request->session()->pull('dms-oauth-state');

        $hasValidState = $state && $state === $request->query('state');

        if (!$hasValidState) {
            return \redirect()->route('dms::auth.login')
                ->with('error', trans('dms::auth.oauth.invalid-state'));
        }

        $accessToken = $oauthProvider->getProvider()->getAccessToken('authorization_code', [
            'code' => $request->input('code'),
        ]);

        $resourceOwner = $oauthProvider->getProvider()->getResourceOwner($accessToken);
        $adminAccount  = $this->loadAdminAccount($oauthProvider, $resourceOwner);

        \auth()->guard('dms')->login($adminAccount);

        return \redirect()->intended(route('dms::index'));
    }

    protected function loadAdminAccount(OauthProvider $oauthProvider, ResourceOwnerInterface $resourceOwner) : OauthAdmin
    {
        $adminAccountDetails = $oauthProvider->getAdminDetailsFromResourceOwner($resourceOwner);

        if (!$oauthProvider->allowsAccount($adminAccountDetails)) {
            throw new HttpResponseException(
                \redirect()->route('dms::auth.login')
                    ->with('error', trans('dms::auth.oauth.invalid-email'))
            );
        }

        $adminsWithEmail = $this->adminRepository->matching(
            $this->adminRepository->criteria()
                ->where(Admin::EMAIL_ADDRESS, '=', $adminAccountDetails->getEmail())
        );

        if ($adminsWithEmail) {
            $this->validateExistingAdminMatches($adminsWithEmail[0], $oauthProvider, $resourceOwner);

            return $adminsWithEmail[0];
        } else {
            return $this->createNewOauthAdminAccount($oauthProvider, $adminAccountDetails, $resourceOwner);
        }
    }

    private function validateExistingAdminMatches(Admin $admin, OauthProvider $oauthProvider, ResourceOwnerInterface $resourceOwner)
    {
        if (!($admin instanceof OauthAdmin)) {
            throw new HttpResponseException(
                \redirect()->route('dms::auth.login')
                    ->with('error', trans('dms::auth.oauth.registered-locally'))
            );
        }

        if ($admin->getOauthProviderName() !== $oauthProvider->getName()) {
            throw new HttpResponseException(
                \redirect()->route('dms::auth.login')
                    ->with('error', trans('dms::auth.oauth.other-provider'))
            );
        }

        if ($admin->getOauthAccountId() !== $resourceOwner->getId()) {
            throw new HttpResponseException(
                \redirect()->route('dms::auth.login')
                    ->with('error', trans('dms::auth.oauth.id-mismatch'))
            );
        }
    }

    protected function createNewOauthAdminAccount(
        OauthProvider $oauthProvider,
        AdminAccountDetails $adminAccountDetails,
        ResourceOwnerInterface $resourceOwner
    ) : OauthAdmin {
        $admin = new OauthAdmin(
            $oauthProvider->getName(),
            $resourceOwner->getId(),
            $adminAccountDetails->getFullName(),
            $adminAccountDetails->getEmail(),
            $adminAccountDetails->getUsername(),
            $oauthProvider->shouldRegisterAsSuperUser()
        );

        $this->adminRepository->save($admin);

        foreach ($this->loadRoles($oauthProvider) as $role) {
            $admin->giveRole($role);
        }

        $this->adminRepository->save($admin);

        return $admin;
    }

    /**
     * @param OauthProvider $oauthProvider
     *
     * @return Role[]
     */
    protected function loadRoles(OauthProvider $oauthProvider) : array
    {
        $roles = [];

        foreach ($oauthProvider->getRoleNames() as $roleName) {
            $matchingRoles = $this->roleRepository->matching(
                $this->roleRepository->criteria()
                    ->where(Role::NAME, '=', $roleName)
            );

            if (!$matchingRoles) {
                $matchingRoles[] = new Role($roleName, Permission::collection());
                $this->roleRepository->saveAll($matchingRoles);
            }

            $roles = array_merge($roles, $matchingRoles);
        }

        return $roles;
    }

    private function getProvider(string $providerName) : OauthProvider
    {
        abort_unless($this->providerCollection->has($providerName), 404);

        return $this->providerCollection->getAll()[$providerName];
    }
}
