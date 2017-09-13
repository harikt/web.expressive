<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Common\Structure\DateTime\DateTime;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;

/**
 * The password reset attempt record entity.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PasswordResetToken extends Entity
{
    const EMAIL = 'email';
    const TOKEN = 'token';
    const CREATED_AT = 'createdAt';

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * PasswordResetAttempt constructor.
     *
     * @param string   $email
     * @param string   $token
     * @param DateTime $createdAt
     */
    public function __construct(string $email, string $token, DateTime $createdAt)
    {
        parent::__construct();

        $this->email     = $email;
        $this->token     = $token;
        $this->createdAt = $createdAt;
    }

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->email)->asString();
        $class->property($this->token)->asString();
        $class->property($this->createdAt)->asObject(DateTime::class);
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }
}
