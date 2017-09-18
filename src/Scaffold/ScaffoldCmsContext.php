<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold;

use Dms\Web\Expressive\Scaffold\Domain\DomainStructure;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScaffoldCmsContext extends ScaffoldContext
{
    /**
     * @var string
     */
    protected $dataSourceNamespace;

    /**
     * @var string
     */
    protected $outputNamespace;

    /**
     * ScaffoldCmsContext constructor.
     *
     * @param string          $rootEntityNamespace
     * @param DomainStructure $domainStructure
     * @param string          $dataSourceNamespace
     * @param string          $outputNamespace
     */
    public function __construct(string $rootEntityNamespace, DomainStructure $domainStructure, string $dataSourceNamespace, string $outputNamespace)
    {
        parent::__construct($rootEntityNamespace, $domainStructure);
        $this->dataSourceNamespace = ltrim($dataSourceNamespace, '\\');
        $this->outputNamespace     = ltrim($outputNamespace, '\\');
    }

    /**
     * @return string
     */
    public function getDataSourceNamespace() : string
    {
        return $this->dataSourceNamespace;
    }

    /**
     * @return string
     */
    public function getOutputNamespace() : string
    {
        return $this->outputNamespace;
    }

    /**
     * @return string
     */
    public function getModuleNamespace() : string
    {
        return $this->outputNamespace . '\\Modules';
    }

    /**
     * @return string
     */
    public function getValueObjectFieldNamespace() : string
    {
        return $this->outputNamespace . '\\Modules\\Fields';
    }
}
