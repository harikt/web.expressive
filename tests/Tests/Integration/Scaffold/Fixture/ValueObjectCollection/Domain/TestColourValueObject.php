<?php declare(strict_types=1);

namespace Dms\Web\Laravel\Tests\Integration\Scaffold\Fixture\ValueObjectCollection\Domain;

use Dms\Common\Structure\Colour\Colour;
use Dms\Common\Structure\Colour\TransparentColour;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;
use Dms\Core\Model\ValueObjectCollection;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestColourValueObject extends ValueObject
{
    const COLOUR = 'colour';
    const TRANSPARENT_COLOUR = 'transparentColour';

    /**
     * @var ValueObjectCollection|Colour[]
     */
    public $colour;

    /**
     * @var ValueObjectCollection|TransparentColour[]
     */
    public $transparentColour;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->colour)->asType(Colour::collectionType());

        $class->property($this->transparentColour)->asType(TransparentColour::collectionType());
    }
}