<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth;

use Dms\Core\Auth\IPermission;
use Dms\Core\Auth\IRole;
use Dms\Core\Auth\Permission;
use Dms\Core\Model\EntityIdCollection;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\Entity;
use Dms\Core\Model\ValueObjectCollection;

/**
 * The role entity.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Role extends Entity implements IRole
{
    const NAME = 'name';
    const USER_IDS = 'userIds';
    const PERMISSIONS = 'permissions';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ValueObjectCollection|Permission[]
     */
    protected $permissions;

    /**
     * @var EntityIdCollection
     */
    protected $userIds;

    /**
     * Role constructor.
     *
     * @param string                             $name
     * @param Permission[]|ValueObjectCollection $permissions
     * @param EntityIdCollection                 $userIds
     */
    public function __construct(string $name, ValueObjectCollection $permissions, EntityIdCollection $userIds = null)
    {
        parent::__construct();
        $this->name        = $name;
        $this->permissions = $permissions;
        $this->userIds     = $userIds ?? new EntityIdCollection();
    }

    /**
     * Defines the structure of this entity.
     *
     * @param ClassDefinition $class
     */
    protected function defineEntity(ClassDefinition $class)
    {
        $class->property($this->name)->asString();

        $class->property($this->permissions)->asType(Permission::collectionType());

        $class->property($this->userIds)->asType(EntityIdCollection::type());
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Gets the permission.
     *
     * @return ValueObjectCollection|IPermission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return EntityIdCollection
     */
    public function getUserIds() : \Dms\Core\Model\EntityIdCollection
    {
        return $this->userIds;
    }
}
