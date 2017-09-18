<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration;

use Dms\Common\Structure\Geo\LatLng;
use Dms\Common\Structure\Geo\Persistence\LatLngMapper;
use Dms\Common\Structure\Geo\Persistence\StreetAddressMapper;
use Dms\Common\Structure\Geo\Persistence\StreetAddressWithLatLngMapper;
use Dms\Common\Structure\Geo\StreetAddress;
use Dms\Common\Structure\Geo\StreetAddressWithLatLng;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GeoPropertyCodeGenerator extends CommonValueObjectPropertyCodeGenerator
{
    /**
     * @return string[]
     */
    protected function getSupportedValueObjectClasses() : array
    {
        return [LatLng::class, StreetAddress::class, StreetAddressWithLatLng::class];
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
        if ($objectClass === LatLng::class) {
            $class   = LatLngMapper::class;
            $columns = [$columnName . '_lat', $columnName . '_lng'];
        } elseif ($objectClass === StreetAddress::class) {
            $class   = StreetAddressMapper::class;
            $columns = [$columnName];
        } elseif ($objectClass === StreetAddressWithLatLng::class) {
            $class   = StreetAddressWithLatLngMapper::class;
            $columns = [$columnName . '_address', $columnName . '_lat', $columnName . '_lng'];
        }

        foreach ($columns as $key => $column) {
            $columns[$key] = '\'' . $column . '\'';
        }

        if (!$isCollection && $property->getType()->isNullable()) {
            $code->getCode()->appendLine('->withIssetColumn(' . reset($columns) . ')');
        }

        $code->addNamespaceImport($class);
        $code->getCode()->append('->using(new ' . $this->getShortClassName($class) . '(' . implode(', ', $columns) . '))');
    }

    protected function doGenerateCmsObjectFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass
    ) {
        if ($objectClass === LatLng::class) {
            $code->getCode()->append('->latLng()');
        } elseif ($objectClass === StreetAddress::class) {
            $code->getCode()->append('->streetAddress()');
        } elseif ($objectClass === StreetAddressWithLatLng::class) {
            $code->getCode()->append('->streetAddressWithLatLng()');
        }
    }
}
