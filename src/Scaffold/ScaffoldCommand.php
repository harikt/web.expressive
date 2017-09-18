<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Web\Expressive\Scaffold\CodeGeneration\ColourPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\Convention\DefaultCodeConvention;
use Dms\Web\Expressive\Scaffold\CodeGeneration\CountryPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\CurrencyPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\DateAndTimePropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\EnumPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\FallbackPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\FilePropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\GeoPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\MoneyPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\PhpCodeBuilderContext;
use Dms\Web\Expressive\Scaffold\CodeGeneration\PropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\Relation\CustomValueObjectCollectionPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\Relation\CustomValueObjectPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\Relation\EntityCollectionPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\Relation\EntityPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\ScalarPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\CodeGeneration\WebPropertyCodeGenerator;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Dms\Web\Expressive\Scaffold\Domain\DomainStructure;
use Dms\Web\Expressive\Scaffold\Domain\DomainStructureLoader;
use Symfony\Component\Console\Command\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * The dms:scaffold command base class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ScaffoldCommand extends Command
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var DomainStructureLoader
     */
    protected $domainStructureLoader;

    /**
     * @var NamespaceDirectoryResolver
     */
    protected $namespaceResolver;

    /**
     * @var PropertyCodeGenerator[]
     */
    protected $propertyCodeGenerators;

    /**
     * ScaffoldCommand constructor.
     *
     * @param Filesystem                 $filesystem
     * @param DomainStructureLoader      $domainStructureLoader
     * @param NamespaceDirectoryResolver $namespaceResolver
     */
    public function __construct(Filesystem $filesystem, DomainStructureLoader $domainStructureLoader, NamespaceDirectoryResolver $namespaceResolver)
    {
        parent::__construct();

        $this->filesystem            = $filesystem;
        $this->domainStructureLoader = $domainStructureLoader;
        $this->namespaceResolver     = $namespaceResolver;

        $convention                   = new DefaultCodeConvention();
        $this->propertyCodeGenerators = [
            new ScalarPropertyCodeGenerator($convention),
            new DateAndTimePropertyCodeGenerator($convention),
            new FilePropertyCodeGenerator($convention),
            new ColourPropertyCodeGenerator($convention),
            new GeoPropertyCodeGenerator($convention),
            new CountryPropertyCodeGenerator($convention),
            new MoneyPropertyCodeGenerator($convention),
            new CurrencyPropertyCodeGenerator($convention),
            new WebPropertyCodeGenerator($convention),
            new EnumPropertyCodeGenerator($convention),
            new CustomValueObjectPropertyCodeGenerator($convention),
            new CustomValueObjectCollectionPropertyCodeGenerator($convention),
            new EntityPropertyCodeGenerator($convention),
            new EntityCollectionPropertyCodeGenerator($convention),
            new FallbackPropertyCodeGenerator($convention),
        ];
    }

    /**
     * @param DomainObjectStructure[] $objects
     * @param string                  $filter
     *
     * @return DomainObjectStructure[]
     */
    protected function filterDomainObjects(array $objects, string $filter): array
    {
        $filter = trim($filter, ' \\');

        return array_filter($objects, function (DomainObjectStructure $object) use ($filter) {
            return str_is($filter, $object->getReflection()->getName());
        });
    }

    protected function createFile(string $filePath, string $code, bool $overwrite)
    {
        $this->filesystem->makeDirectory(dirname($filePath), 0755, true, true);

        if (!$overwrite && $this->filesystem->exists($filePath)) {
            return;
        }

        $this->filesystem->put($filePath, $code);
    }

    /**
     * @param DomainStructure       $domain
     * @param DomainObjectStructure $object
     * @param string                $propertyName
     *
     * @return PropertyCodeGenerator
     * @throws InvalidArgumentException
     */
    protected function getCodeGeneratorFor(DomainStructure $domain, DomainObjectStructure $object, string $propertyName): PropertyCodeGenerator
    {
        foreach ($this->propertyCodeGenerators as $codeGenerator) {
            if ($codeGenerator->supports($domain, $object, $propertyName)) {
                return $codeGenerator;
            }
        }

        throw InvalidArgumentException::format('Cannot find property generator for \'%s\'', $propertyName);
    }

    /**
     * @param string                $stubFile
     * @param PhpCodeBuilderContext $code
     * @param array                 $replacements
     *
     * @return string
     */
    protected function buildCodeFile(string $stubFile, PhpCodeBuilderContext $code, array $replacements)
    {
        $php = $this->filesystem->get($stubFile);

        $imports               = [];
        $properties            = [];
        $constructorParameters = [];
        $initializers          = [];

        foreach ($code->getNamespaceImports() as $import) {
            $imports[] = 'use ' . $import . ';';
        }

        foreach ($code->getConstructorParameters() as $classType => $name) {
            $indent = '    ';

            $property = $indent . '/**' . PHP_EOL;
            $property .= $indent . ' * @var ' . basename($classType) . PHP_EOL;
            $property .= $indent . ' */' . PHP_EOL;
            $property .= $indent . 'protected $' . $name . ';' . PHP_EOL;

            $properties[]            = $property;
            $constructorParameters[] = $classType . ' $' . $name;
            $initializers[]          = $indent . $indent . '$this->' . $name . ' = $' . $name . ';';
        }

        $php = strtr(
            $php,
            [
                '{imports}'            => implode(PHP_EOL, $imports),
                '{properties}'         => implode(PHP_EOL, $properties),
                '{constructor_params}' => $constructorParameters ? ', ' . implode(', ', $constructorParameters) : '',
                '{initializers}'       => implode(PHP_EOL, $initializers),
            ] + $replacements
        );

        return $php;
    }
}
