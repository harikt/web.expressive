<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration;

use Dms\Common\Structure\FileSystem\File;
use Dms\Common\Structure\FileSystem\Image;
use Dms\Common\Structure\FileSystem\Persistence\FileMapper;
use Dms\Common\Structure\FileSystem\Persistence\ImageMapper;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FilePropertyCodeGenerator extends CommonValueObjectPropertyCodeGenerator
{
    /**
     * @return string[]
     */
    protected function getSupportedValueObjectClasses() : array
    {
        return [File::class];
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
        if ($property->getType()->nonNullable()->isSubsetOf(Image::type())) {
            $class = ImageMapper::class;
        } else {
            $class = FileMapper::class;
        }

        if (!$isCollection && $property->getType()->isNullable()) {
            $code->getCode()->appendLine('->withIssetColumn(\'' . $columnName . '\')');
        }

        $code->addNamespaceImport($class);
        $basePath = $this->getStorageDirectoryCode($object);
        $code->getCode()->append('->using(new ' . $this->getShortClassName($class) . '(\'' . $columnName . '\', \'' . $columnName . '_file_name\', ' . $basePath . '))');
    }

    protected function appendsRequiredMethodCall() : bool
    {
        return true;
    }

    protected function doGenerateCmsObjectFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass
    ) {
        $code->getCode()->appendLine();
        $code->getCode()->indent++;

        if ($property->getType()->nonNullable()->isSubsetOf(Image::type())) {
            $code->getCode()->appendLine('->image()');
        } else {
            $code->getCode()->appendLine('->file()');
        }

        if (!$property->getType()->isNullable()) {
            $code->getCode()->appendLine('->required()');
        }

        $code->getCode()->append('->moveToPathWithRandomFileName(' . $this->getStorageDirectoryCode($object) . ')');

        $code->getCode()->indent--;

    }

    protected function getStorageDirectoryCode(DomainObjectStructure $object) : string
    {
        return 'public_path(\'app/' . snake_case($object->getReflection()->getShortName()) . '\')';
    }
}