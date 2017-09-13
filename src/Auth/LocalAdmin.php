<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Auth\IHashedPassword;
use Dms\Core\Model\EntityIdCollection;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Web\Expressive\Auth\Password\HashedPassword;
use Dms\Web\Expressive\Auth\Persistence\Mapper\AdminMapper;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Mail\Message;

/**
 * The laravel admin entity.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class LocalAdmin extends Admin implements CanResetPassword
{
    const PASSWORD = 'password';
    const REMEMBER_TOKEN = 'rememberToken';

    /**
     * @var HashedPassword
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $rememberToken;

    /**
     * LocalAdmin constructor.
     *
     * @param string                  $fullName
     * @param EmailAddress            $emailAddress
     * @param string                  $username
     * @param IHashedPassword         $password
     * @param bool                    $isSuperUser
     * @param bool                    $isBanned
     * @param EntityIdCollection|null $roleIds
     */
    public function __construct(
        string $fullName,
        EmailAddress $emailAddress,
        string $username,
        IHashedPassword $password,
        bool $isSuperUser = false,
        bool $isBanned = false,
        EntityIdCollection $roleIds = null
    ) {
        parent::__construct($fullName, $emailAddress, $username, $isSuperUser, $isBanned, $roleIds);

        $this->password = HashedPassword::from($password);
    }

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        parent::defineEntity($class);

        $class->property($this->password)->asObject(HashedPassword::class);

        $class->property($this->rememberToken)->nullable()->asString();
    }

    /**
     * @return IHashedPassword
     */
    public function getPassword() : IHashedPassword
    {
        return $this->password;
    }

    /**
     * @param IHashedPassword $password
     */
    public function setPassword(IHashedPassword $password)
    {
        $this->password = $password;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() : string
    {
        return AdminMapper::AUTH_PASSWORD_COLUMN;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     *
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
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

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->emailAddress->asString();
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        \Mail::send('dms::auth.email.password', ['token' => $token], function (Message $m) {
            $m->subject('Your DMS account password reset');
            $m->to($this->emailAddress->asString(), $this->fullName);
        });
    }
}
