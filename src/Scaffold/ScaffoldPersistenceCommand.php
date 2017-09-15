<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold;

use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Core\Exception\InvalidOperationException;
use Dms\Core\Model\Object\Entity;
use Dms\Web\Expressive\Scaffold\CodeGeneration\PhpCodeBuilderContext;
use Dms\Web\Expressive\Scaffold\Domain\DomainObjectStructure;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The dms:scaffold:persistence command
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScaffoldPersistenceCommand extends ScaffoldCommand
{
    protected function configure()
    {
        $this
            ->setName('dms:scaffold:persistence')
            ->addArgument('entity_namespace', InputArgument::OPTIONAL, 'The namespace of the entities.', 'App\\Domain\\Entities')
            ->addArgument('output_abstract_namespace', InputArgument::OPTIONAL, 'The path to place the repository interfaces.', 'App\\Domain\\Services\\Persistence')
            ->addArgument('output_implementation_namespace', InputArgument::OPTIONAL, 'The path to place the repository and mapper implementations.', 'App\\Infrastructure\\Persistence')
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Whether to overwrite existing files', false)
            ->addOption('filter', null, InputOption::VALUE_OPTIONAL, 'A filter pattern to restrict which entities are scaffolded e.g App\\Domain\\Entities\\Specific\\*')
            ->setDescription('Scaffolds the persistence layer for a set of entities')
            ->setHelp('Scaffolds the persistence layer for a set of entities');
    }

    /**
     * Execute the console command.
     *
     * @throws InvalidOperationException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain       = $this->domainStructureLoader->loadDomainStructure($input->getArgument('entity_namespace'));

        $context = new ScaffoldPersistenceContext(
            $input->getArgument('entity_namespace'),
            $domain,
            $input->getArgument('output_abstract_namespace'),
            $input->getArgument('output_implementation_namespace')
        );

        $overwrite    = $input->hasOption('overwrite') && (bool)$input->getOption('overwrite');
        $entities     = $domain->getRootEntities();
        $valueObjects = $domain->getRootValueObjects();

        if ($input->hasOption('filter') && $input->getOption('filter')) {
            $entities     = $this->filterDomainObjects($entities, $input->getOption('filter'));
            $valueObjects = $this->filterDomainObjects($valueObjects, $input->getOption('filter'));
        }

        if (!$valueObjects && !$entities) {
            $output->writeln('<error>No entities found under ' . $context->getRootEntityNamespace() . ' namespace</error>');

            return;
        }

        foreach ($entities as $entity) {
            list($repositoryClass, $repositoryShortClassName) = $this->generateRepositoryInterface($context, $entity, $overwrite);
            $this->generateEntityMapper($context, $entity, $overwrite);
            $this->generateRepositoryImplementation($context, $entity, $repositoryClass, $repositoryShortClassName, $overwrite);
        }

        foreach ($valueObjects as $valueObject) {
            $this->generateValueObjectMapper($context, $valueObject, $overwrite);
        }

        $output->writeln('<info>Done!</info>');
    }

    private function generateRepositoryInterface(ScaffoldPersistenceContext $context, DomainObjectStructure $entity, bool $overwrite)
    {
        $entityName        = $entity->getReflection()->getShortName();
        $relativeNamespace = $context->getRelativeObjectNamespace($entity);

        $repositoryName      = 'I' . $entityName . 'Repository';
        $repositoryNamespace = $context->getOutputAbstractNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
        $repositoryDirectory = $this->namespaceResolver->getDirectoryFor($repositoryNamespace);
        $repositoryClass     = $repositoryNamespace . '\\' . $repositoryName;

        $php = $this->filesystem->get(__DIR__ . '/Stubs/Persistence/RepositoryInterface.php.stub');

        $php = strtr($php, [
            '{namespace}'   => $repositoryNamespace,
            '{name}'        => $repositoryName,
            '{entity}'      => $entity->getDefinition()->getClassName(),
            '{entity_name}' => $entityName,
        ]);

        $this->createFile(PathHelper::combine($repositoryDirectory, $repositoryName . '.php'), $php, $overwrite);

        return [$repositoryClass, $repositoryName];
    }

    private function generateEntityMapper(ScaffoldPersistenceContext $context, DomainObjectStructure $entity, bool $overwrite)
    {
        $entityName        = $entity->getReflection()->getShortName();
        $relativeNamespace = $context->getRelativeObjectNamespace($entity);

        $mapperName      = $entityName . 'Mapper';
        $mapperNamespace = $context->getOutputImplementationNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
        $mapperDirectory = $this->namespaceResolver->getDirectoryFor($mapperNamespace);

        $mappingCodeContext = $this->generatePropertyBindingCode($context, $entity, 2);

        $php = $this->buildCodeFile(
            __DIR__ . '/Stubs/Persistence/EntityMapper.php.stub',
            $mappingCodeContext,
            [
                '{namespace}'   => $mapperNamespace,
                '{name}'        => $mapperName,
                '{entity}'      => $entity->getDefinition()->getClassName(),
                '{entity_name}' => $entityName,
                '{table_name}'  => str_plural(snake_case($entityName)),
                '{mapping}'     => $mappingCodeContext->getCode()->getCode(),
            ]
        );

        $this->createFile(PathHelper::combine($mapperDirectory, $mapperName . '.php'), $php, $overwrite);
    }

    protected function generatePropertyBindingCode(ScaffoldPersistenceContext $context, DomainObjectStructure $object, int $indent) : PhpCodeBuilderContext
    {
        $code = new PhpCodeBuilderContext();

        $code->getCode()->indent = $indent;

        foreach ($object->getDefinition()->getProperties() as $property) {
            if ($property->getName() === Entity::ID) {
                continue;
            }

            $this->getCodeGeneratorFor($context->getDomainStructure(), $object, $property->getName())->generatePersistenceMappingCode(
                $context,
                $code,
                $object,
                $property->getName()
            );

            $code->getCode()->appendLine(';');
            $code->getCode()->appendLine();
        }

        return $code;
    }

    private function generateRepositoryImplementation(
        ScaffoldPersistenceContext $context,
        DomainObjectStructure $entity,
        string $interfaceClass,
        string $interfaceName,
        bool $overwrite
    ) {
        $entityName        = $entity->getReflection()->getShortName();
        $relativeNamespace = $context->getRelativeObjectNamespace($entity);

        $repositoryName      = 'Db' . $entityName . 'Repository';
        $repositoryNamespace = $context->getOutputImplementationNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
        $repositoryDirectory = $this->namespaceResolver->getDirectoryFor($repositoryNamespace);

        $php = $this->filesystem->get(__DIR__ . '/Stubs/Persistence/RepositoryImplementation.php.stub');

        $php = strtr($php, [
            '{namespace}'      => $repositoryNamespace,
            '{name}'           => $repositoryName,
            '{entity}'         => $entity->getDefinition()->getClassName(),
            '{entity_name}'    => $entityName,
            '{interface}'      => $interfaceClass,
            '{interface_name}' => $interfaceName,
        ]);

        $this->createFile(PathHelper::combine($repositoryDirectory, $repositoryName . '.php'), $php, $overwrite);
    }

    private function generateValueObjectMapper(ScaffoldPersistenceContext $context, DomainObjectStructure $valueObject, bool $overwrite)
    {
        $valueObjectName   = $valueObject->getReflection()->getShortName();
        $relativeNamespace = $context->getRelativeObjectNamespace($valueObject);

        $mapperName      = $valueObjectName . 'Mapper';
        $mapperNamespace = $context->getOutputImplementationNamespace() . ($relativeNamespace ? '\\' . $relativeNamespace : '');
        $mapperDirectory = $this->namespaceResolver->getDirectoryFor($mapperNamespace);

        $mappingCodeContext = $this->generatePropertyBindingCode($context, $valueObject, 2);

        if ($valueObject->hasEntityRelations()) {
            $stubFile = __DIR__ . '/Stubs/Persistence/ValueObjectMapper.php.stub';
        } else {
            $stubFile = __DIR__ . '/Stubs/Persistence/IndependentValueObjectMapper.php.stub';
        }

        $php = $this->buildCodeFile(
            $stubFile,
            $mappingCodeContext,
            [
                '{namespace}'         => $mapperNamespace,
                '{name}'              => $mapperName,
                '{value_object}'      => $valueObject->getDefinition()->getClassName(),
                '{value_object_name}' => $valueObjectName,
                '{mapping}'           => $mappingCodeContext->getCode()->getCode(),
            ]
        );

        $this->createFile(PathHelper::combine($mapperDirectory, $mapperName . '.php'), $php, $overwrite);
    }

    protected function createFile(string $filePath, string $code, bool $overwrite)
    {
        $this->filesystem->makeDirectory(dirname($filePath), 0755, true, true);

        if (!$overwrite && $this->filesystem->exists($filePath)) {
            return;
        }

        $this->filesystem->put($filePath, $code);
    }
}
