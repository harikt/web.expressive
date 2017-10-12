<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Oauth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Zend\Expressive\Router\RouterInterface;

/**
 * The oauth provider class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class OauthProvider
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var AbstractProvider
     */
    protected $provider;

    /**
     * @var bool
     */
    protected $isSuperUser;

    /**
     * @var string[]
     */
    protected $roleNames;

    /**
     * @var string[]
     */
    protected $allowedEmails;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * OauthProvider constructor.
     *
     * @param string          $name
     * @param string          $label
     * @param string          $clientId
     * @param string          $clientSecret
     * @param bool            $isSuperUser
     * @param string[]        $roleNames
     * @param string[]        $allowedEmails
     * @param RouterInterface $router
     *
     * @internal param AbstractProvider $provider
     */
    final public function __construct(
        string $name,
        string $label,
        string $clientId,
        string $clientSecret,
        bool $isSuperUser,
        array $roleNames,
        array $allowedEmails,
        RouterInterface $router
    ) {
        $this->name          = $name;
        $this->label         = $label;
        $this->isSuperUser   = $isSuperUser;
        $this->roleNames     = $roleNames;
        $this->allowedEmails = $allowedEmails;
        $this->router = $router;
        $this->provider      = $this->loadProvider($clientId, $clientSecret);
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return AbstractProvider
     */
    abstract protected function loadProvider(string $clientId, string $clientSecret) : AbstractProvider;

    /**
     * @param ResourceOwnerInterface $resourceOwner
     *
     * @return AdminAccountDetails
     */
    abstract public function getAdminDetailsFromResourceOwner(ResourceOwnerInterface $resourceOwner) : AdminAccountDetails;

    /**
     * @param array $config
     *
     * @return OauthProvider
     */
    public static function fromConfiguration(array $config)
    {
        return new static(
            $config['name'],
            $config['label'],
            $config['client-id'],
            $config['client-secret'],
            $config['super-user'] ?? false,
            $config['roles'] ?? [],
            $config['allowed-emails']
        );
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * @return AbstractProvider
     */
    public function getProvider() : AbstractProvider
    {
        return $this->provider;
    }

    /**
     * @return boolean
     */
    public function shouldRegisterAsSuperUser()
    {
        return $this->isSuperUser;
    }

    /**
     * @return string[]
     */
    public function getRoleNames()
    {
        return $this->roleNames;
    }

    /**
     * @return string[]
     */
    public function getAllowedEmails()
    {
        return $this->allowedEmails;
    }

    /**
     * @param AdminAccountDetails $adminAccountDetails
     *
     * @return bool
     */
    public function allowsAccount(AdminAccountDetails $adminAccountDetails) : bool
    {
        foreach ($this->allowedEmails as $allowedEmail) {
            if (str_is($allowedEmail, $adminAccountDetails->getEmail()->asString())) {
                return true;
            }
        }

        return false;
    }
}
