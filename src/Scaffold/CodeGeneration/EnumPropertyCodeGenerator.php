<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration;

use Dms\Core\Model\Object\Enum;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Core\Model\Type\ObjectType;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\Domain\DomainStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EnumPropertyCodeGenerator extends PropertyCodeGenerator
{
    /**
     * @param DomainStructure             $domain
     * @param DomainObjectStructure       $object
     * @param FinalizedPropertyDefinition $property
     *
     * @return bool
     */
    protected function doesSupportProperty(DomainStructure $domain, DomainObjectStructure $object, FinalizedPropertyDefinition $property) : bool
    {
        return $property->getType()->nonNullable()->isSubsetOf(Enum::type());
    }


    /**
     * @param ScaffoldPersistenceContext  $context
     * @param PhpCodeBuilderContext       $code
     * @param DomainObjectStructure       $object
     * @param FinalizedPropertyDefinition $property
     * @param string                      $propertyReference
     * @param string                      $columnName
     */
    protected function doGeneratePersistenceMappingCode(
        ScaffoldPersistenceContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        string $propertyReference,
        string $columnName
    ) {
        $php = '$map->enum(' . $propertyReference . ')->to(\'' . $columnName . '\')';

        $php .= '->usingValuesFromConstants()';

        $code->getCode()->append($php);
    }

    /**
     * @param ScaffoldCmsContext          $context
     * @param PhpCodeBuilderContext       $code
     * @param DomainObjectStructure       $object
     * @param FinalizedPropertyDefinition $property
     * @param string                      $propertyReference
     * @param string                      $fieldName
     * @param string                      $fieldLabel
     */
    protected function doGenerateCmsFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        string $propertyReference,
        string $fieldName,
        string $fieldLabel
    ) {
        $code->getCode()->append('Field::create(\'' . $fieldName . '\', \'' . $fieldLabel . '\')');

        /** @var ObjectType $objectType */
        $objectType = $property->getType()->nonNullable();
        /** @var string|Enum $enumClass */
        $enumClass = $objectType->getClass();
        $code->addNamespaceImport($enumClass);

        $code->getCode()->appendLine('->enum(' . $this->getShortClassName($enumClass) . '::class, [');

        $code->getCode()->indent++;

        foreach ($enumClass::getOptions() as $constant => $option) {
            $label = $this->codeConvention->getCmsFieldLabel((string)$option);
            $code->getCode()->appendLine($this->getShortClassName($enumClass) . '::' . $constant . ' => ' . var_export($label, true) . ',');
        }

        $code->getCode()->indent--;
        $code->getCode()->append('])');

        if (!$property->getType()->isNullable()) {
            $code->getCode()->append('->required()');
        }
    }
}
