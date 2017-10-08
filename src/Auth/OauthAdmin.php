<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Exception\InvalidOperationException;
use Dms\Core\Model\EntityIdCollection;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Web\Expressive\Auth\Persistence\Mapper\AdminMapper;

/**
 * The laravel admin entity.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OauthAdmin extends Admin
{
    const OAUTH_PROVIDER_NAME = 'oauthProviderName';
    const OAUTH_ACCOUNT_ID = 'oauthAccountId';

    /**
     * @var string
     */
    protected $oauthProviderName;

    /**
     * @var string
     */
    protected $oauthAccountId;

    /**
     * OauthAdmin constructor.
     *
     * @param string                  $oauthProviderName
     * @param string                  $oauthAccountId
     * @param string                  $fullName
     * @param EmailAddress            $emailAddress
     * @param string                  $username
     * @param bool                    $isSuperUser
     * @param bool                    $isBanned
     * @param EntityIdCollection|null $roleIds
     */
    public function __construct(
        string $oauthProviderName,
        string $oauthAccountId,
        string $fullName,
        EmailAddress $emailAddress,
        string $username,
        bool $isSuperUser = false,
        bool $isBanned = false,
        EntityIdCollection $roleIds = null
    ) {
        parent::__construct($fullName, $emailAddress, $username, $isSuperUser, $isBanned, $roleIds);

        $this->oauthProviderName = $oauthProviderName;
        $this->oauthAccountId    = $oauthAccountId;
    }

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        parent::defineEntity($class);

        $class->property($this->oauthProviderName)->asString();
        $class->property($this->oauthAccountId)->asString();
    }

    /**
     * @return string
     */
    public function getOauthProviderName() : string
    {
        return $this->oauthProviderName;
    }

    /**
     * @return string
     */
    public function getOauthAccountId() : string
    {
        return $this->oauthAccountId;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     * @throws InvalidOperationException
     */
    public function getAuthPassword() : string
    {
        throw InvalidOperationException::methodCall(__METHOD__, 'not supported');
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     * @throws InvalidOperationException
     */
    public function getRememberToken()
    {
        throw InvalidOperationException::methodCall(__METHOD__, 'not supported');
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @throws InvalidOperationException
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return AdminMapper::AUTH_REMEMBER_TOKEN_COLUMN;
    }
}
