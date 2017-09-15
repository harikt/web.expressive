<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration;

use Dms\Common\Structure\Web\EmailAddress;
use Dms\Common\Structure\Web\Html;
use Dms\Common\Structure\Web\IpAddress;
use Dms\Common\Structure\Web\Persistence\EmailAddressMapper;
use Dms\Common\Structure\Web\Persistence\HtmlMapper;
use Dms\Common\Structure\Web\Persistence\IpAddressMapper;
use Dms\Common\Structure\Web\Persistence\UrlMapper;
use Dms\Common\Structure\Web\Url;
use Dms\Core\Model\Object\FinalizedPropertyDefinition;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\ScaffoldCmsContext;
use Dms\Web\Expressive\Scaffold\ScaffoldPersistenceContext;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class WebPropertyCodeGenerator extends CommonValueObjectPropertyCodeGenerator
{
    /**
     * @return string[]
     */
    protected function getSupportedValueObjectClasses() : array
    {
        return [Html::class, EmailAddress::class, IpAddress::class, Url::class];
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
        if ($objectClass === EmailAddress::class) {
            $class = EmailAddressMapper::class;
        } elseif ($objectClass === Html::class) {
            $class = HtmlMapper::class;
        } elseif ($objectClass === IpAddress::class) {
            $class = IpAddressMapper::class;
        } elseif ($objectClass === Url::class) {
            $class = UrlMapper::class;
        }

        $code->addNamespaceImport($class);

        if (!$isCollection && $property->getType()->isNullable()) {
            $code->getCode()->appendLine('->withIssetColumn(\'' . $columnName . '\')');
        }

        $code->getCode()->append('->using(new ' . $this->getShortClassName($class) . '(\'' . $columnName . '\'))');

    }

    protected function doGenerateCmsObjectFieldCode(
        ScaffoldCmsContext $context,
        PhpCodeBuilderContext $code,
        DomainObjectStructure $object,
        FinalizedPropertyDefinition $property,
        bool $isCollection,
        string $objectClass
    ) {
        if ($objectClass === EmailAddress::class) {
            $code->getCode()->append('->email()');
        } elseif ($objectClass === Html::class) {
            $code->getCode()->append('->html()');
        } elseif ($objectClass === IpAddress::class) {
            $code->getCode()->append('->ipAddress()');
        } elseif ($objectClass === Url::class) {
            $code->getCode()->append('->url()');
        }
    }
}