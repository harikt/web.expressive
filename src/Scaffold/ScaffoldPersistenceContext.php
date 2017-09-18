<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold;

use Dms\Web\Expressive\Scaffold\Domain\DomainStructure;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScaffoldPersistenceContext extends ScaffoldContext
{
    /**
     * @var string
     */
    protected $outputAbstractNamespace;

    /**
     * @var string
     */
    protected $outputImplementationNamespace;

    /**
     * ScaffoldPersistenceContext constructor.
     *
     * @param string          $rootEntityNamespace
     * @param DomainStructure $domainStructure
     * @param string          $outputAbstractNamespace
     * @param string          $outputImplementationNamespace
     */
    public function __construct(string $rootEntityNamespace, DomainStructure $domainStructure, string $outputAbstractNamespace, string $outputImplementationNamespace)
    {
        parent::__construct($rootEntityNamespace, $domainStructure);
        $this->outputAbstractNamespace       = ltrim($outputAbstractNamespace, '\\');
        $this->outputImplementationNamespace = ltrim($outputImplementationNamespace, '\\');
    }

    /**
     * @return string
     */
    public function getOutputAbstractNamespace(): string
    {
        return $this->outputAbstractNamespace;
    }

    /**
     * @return string
     */
    public function getOutputImplementationNamespace(): string
    {
        return $this->outputImplementationNamespace;
    }
}
