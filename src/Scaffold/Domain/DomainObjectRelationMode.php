<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\Domain;

use Dms\Core\Model\Object\Enum;
use Dms\Core\Model\Object\PropertyTypeDefiner;

/**
 * The domain object relation mode enum
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DomainObjectRelationMode extends Enum
{
    const TO_ONE = 'to-one';
    const TO_MANY = 'to-many';

    /**
     * Defines the type of the options contained within the enum.
     *
     * @param PropertyTypeDefiner $values
     *
     * @return void
     */
    protected function defineEnumValues(PropertyTypeDefiner $values)
    {
        $values->asString();
    }

    /**
     * @return DomainObjectRelationMode
     */
    public static function toOne() : self
    {
        return new self(self::TO_ONE);
    }

    /**
     * @return DomainObjectRelationMode
     */
    public static function toMany() : self
    {
        return new self(self::TO_MANY);
    }
}
