<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Core\Auth\IAdmin;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Exception\InvalidOperationException;
use Dms\Core\Model\EntityIdCollection;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;
use Dms\Library\Metadata\Domain\IHasMetadata;
use Dms\Library\Metadata\Domain\MetadataTrait;
use Dms\Library\Metadata\Domain\ObjectMetadata;
use Dms\Web\Expressive\Auth\Persistence\Mapper\AdminMapper;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * The laravel admin entity.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Admin extends Entity implements IAdmin, Authenticatable, IHasMetadata
{
    use MetadataTrait;

    const FULL_NAME = 'fullName';
    const EMAIL_ADDRESS = 'emailAddress';
    const USERNAME = 'username';
    const IS_SUPER_USER = 'isSuperUser';
    const IS_BANNED = 'isBanned';
    const ROLE_IDS = 'roleIds';

    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var EmailAddress
     */
    protected $emailAddress;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var bool
     */
    protected $isSuperUser;

    /**
     * @var bool
     */
    protected $isBanned;

    /**
     * @var EntityIdCollection
     */
    protected $roleIds;

    /**
     * Admin constructor.
     *
     * @param string                  $fullName
     * @param EmailAddress            $emailAddress
     * @param string                  $username
     * @param bool                    $isSuperUser
     * @param bool                    $isBanned
     * @param EntityIdCollection|null $roleIds
     */
    public function __construct(
        string $fullName,
        EmailAddress $emailAddress,
        string $username,
        bool $isSuperUser = false,
        bool $isBanned = false,
        EntityIdCollection $roleIds = null
    ) {
        parent::__construct();

        $this->fullName     = $fullName;
        $this->emailAddress = $emailAddress;
        $this->username     = $username;
        $this->isSuperUser  = $isSuperUser;
        $this->isBanned     = $isBanned;
        $this->roleIds      = $roleIds ?: new EntityIdCollection();
        $this->metadata     = new ObjectMetadata();

        InvalidArgumentException::verify(strlen($this->username) > 0, 'Username cannot be empty');
    }

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->fullName)->asString();

        $class->property($this->emailAddress)->asObject(EmailAddress::class);

        $class->property($this->username)->asString();

        $class->property($this->isSuperUser)->asBool();

        $class->property($this->isBanned)->asBool();

        $class->property($this->roleIds)->asType(EntityIdCollection::type());

        $this->defineMetadata($class);
    }

    /**
     * @return string
     */
    public function getFullName() : string
    {
        return $this->fullName;
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddressObject() : EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getEmailAddress() : string
    {
        return $this->emailAddress->asString();
    }

    /**
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @param EmailAddress $emailAddress
     */
    public function setEmailAddress(EmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return boolean
     */
    public function isSuperUser() : bool
    {
        return $this->isSuperUser;
    }

    /**
     * @return boolean
     */
    public function isBanned() : bool
    {
        return $this->isBanned;
    }

    /**
     * @return EntityIdCollection
     */
    public function getRoleIds() : EntityIdCollection
    {
        return $this->roleIds;
    }

    /**
     * @param Role $role
     *
     * @return void
     * @throws InvalidOperationException
     */
    public function giveRole(Role $role)
    {
        if (!$this->hasId()) {
            throw InvalidOperationException::format('The user must have an id');
        }

        if (!$role->hasId()) {
            throw InvalidOperationException::format('The supplied role must have an id');
        }

        if (!$this->roleIds->contains($role->getId())) {
            $this->roleIds[] = $role->getId();
        }

        if (!$role->getUserIds()->contains($this->getId())) {
            $role->getUserIds()[] = $this->getId();
        }
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName() : string
    {
        return AdminMapper::AUTH_IDENTIFIER_COLUMN;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }
}
