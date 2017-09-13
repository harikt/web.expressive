<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Oauth\Provider;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Web\Expressive\Auth\Oauth\AdminAccountDetails;
use Dms\Web\Expressive\Auth\Oauth\OauthProvider;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * The google oauth provider class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GoogleOauthProvider extends OauthProvider
{
    /**
     * @var Google
     */
    protected $provider;

    /**
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return AbstractProvider
     */
    protected function loadProvider(string $clientId, string $clientSecret) : AbstractProvider
    {
        return new Google([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri'  => route('dms::auth.oauth.response', $this->name),
        ]);
    }

    /**
     * @param ResourceOwnerInterface $resourceOwner
     *
     * @return AdminAccountDetails
     */
    public function getAdminDetailsFromResourceOwner(ResourceOwnerInterface $resourceOwner) : AdminAccountDetails
    {
        /** @var GoogleUser $resourceOwner */

        return new AdminAccountDetails(
            $resourceOwner->getFirstName() . ' ' . $resourceOwner->getLastName(),
            $resourceOwner->getEmail(),
            new EmailAddress($resourceOwner->getEmail())
        );
    }
}
