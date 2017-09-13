<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Oauth;

use Dms\Common\Structure\Web\EmailAddress;

/**
 * The account details required from the oauth login.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminAccountDetails
{
    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var EmailAddress
     */
    protected $email;

    /**
     * AdminAccountDetails constructor.
     *
     * @param string       $fullName
     * @param string       $username
     * @param EmailAddress $email
     */
    public function __construct(string $fullName, string $username, EmailAddress $email)
    {
        $this->fullName = $fullName;
        $this->username = $username;
        $this->email    = $email;
    }

    /**
     * @return string
     */
    public function getFullName() : string
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @return EmailAddress
     */
    public function getEmail() : EmailAddress
    {
        return $this->email;
    }
}
