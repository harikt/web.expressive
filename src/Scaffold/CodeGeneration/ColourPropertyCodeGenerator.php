<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration;

use Dms\Common\Structure\Colour\Colour;
use Dms\Common\Structure\Colour\Mapper\ColourMapper;
use Dms\Common\Structure\Colour\Mapper\TransparentColourMapper;
use Dms\Common\Structure\Colour\TransparentColour;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ColourPropertyCodeGenerator extends CommonValueObjectPropertyCodeGenerator
{
    /**
     * @return string[]
     */
    protected function getSupportedValueObjectClasses() : array
    {
        return [Colour::class, TransparentColour::class];
    }

    protected function doGeneratePersistenceMappingObjectMapperCode(
        ScaffoldPersistenceContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass,
        string $columnName
    ) {
        if (!$isCollection && $property->getType()->isNullable()) {
            $code->getCode()->appendLine('->withIssetColumn(\'' . $columnName . '\')');
        }

        if ($objectClass === Colour::class) {
            $class  = ColourMapper::class;
            $method = 'asHexString';
        } else {
            $class  = TransparentColourMapper::class;
            $method = 'asRgbaString';;
        }

        $code->addNamespaceImport($class);
        $code->getCode()->append('->using(' . $this->getShortClassName($class) . '::' . $method . '(\'' . $columnName . '\'))');

    }

    protected function doGenerateCmsObjectFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass
    ) {
        if ($objectClass === Colour::class) {
            $code->getCode()->append('->colour()');
        } else {
            $code->getCode()->append('->colourWithTransparency()');
        }
    }
}