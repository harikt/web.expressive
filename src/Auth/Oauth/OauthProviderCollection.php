<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Oauth;

use Dms\Core\Exception\InvalidArgumentException;

/**
 * The oauth provider class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OauthProviderCollection
{
    /**
     * @var OauthProvider[]
     */
    protected $oauthProviders = [];

    /**
     * OauthProviderCollection constructor.
     *
     * @param OauthProvider[] $oauthProviders
     */
    public function __construct(array $oauthProviders)
    {
        InvalidArgumentException::verifyAllInstanceOf(__METHOD__, 'oauthProviders', $oauthProviders, OauthProvider::class);

        foreach ($oauthProviders as $oauthProvider) {
            $this->oauthProviders[$oauthProvider->getName()] = $oauthProvider;
        }
    }

    /**
     * @return OauthProvider[]
     */
    public function getAll() : array
    {
        return $this->oauthProviders;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name) : bool
    {
        return isset($this->oauthProviders[$name]);
    }
}
