<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Tests\Integration\Scaffold\Fixture\ValueObject\Domain;

use Dms\Common\Structure\Colour\Colour;
use Dms\Common\Structure\Colour\TransparentColour;
use Dms\Core\Model\Object\ClassDefinition;
use Dms\Core\Model\Object\ValueObject;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TestColourValueObject extends ValueObject
{
    const COLOUR = 'colour';
    const NULLABLE_COLOUR = 'nullableColour';
    const TRANSPARENT_COLOUR = 'transparentColour';
    const NULLABLE_TRANSPARENT_COLOUR = 'nullableTransparentColour';

    /**
     * @var Colour
     */
    public $colour;

    /**
     * @var Colour|null
     */
    public $nullableColour;

    /**
     * @var TransparentColour
     */
    public $transparentColour;

    /**
     * @var TransparentColour|null
     */
    public $nullableTransparentColour;

    /**
     * Defines the structure of this class.
     *
     * @param ClassDefinition $class
     */
    protected function define(ClassDefinition $class)
    {
        $class->property($this->colour)->asObject(Colour::class);

        $class->property($this->nullableColour)->nullable()->asObject(Colour::class);

        $class->property($this->transparentColour)->asObject(TransparentColour::class);

        $class->property($this->nullableTransparentColour)->nullable()->asObject(TransparentColour::class);
    }
}
