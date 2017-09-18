<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\Domain;

use Dms\Core\Model\Object\FinalizedPropertyDefinition;

/**
 * The domain object relation class
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DomainObjectRelation
{
    /**
     * @var DomainObjectRelationMode
     */
    protected $mode;

    /**
     * @var FinalizedPropertyDefinition
     */
    protected $definition;

    /**
     * @var DomainObjectStructure
     */
    protected $relatedObject;

    /**
     * @var DomainObjectRelation|null
     */
    protected $inverseRelation;

    /**
     * DomainObjectRelation constructor.
     *
     * @param DomainObjectRelationMode    $mode
     * @param FinalizedPropertyDefinition $definition
     * @param DomainObjectStructure       $relatedObject
     */
    public function __construct(DomainObjectRelationMode $mode, FinalizedPropertyDefinition $definition, DomainObjectStructure $relatedObject)
    {
        $this->mode          = $mode;
        $this->definition    = $definition;
        $this->relatedObject = $relatedObject;
    }

    /**
     * @return DomainObjectRelationMode
     */
    public function getMode(): DomainObjectRelationMode
    {
        return $this->mode;
    }

    /**
     * @return bool
     */
    public function isToOne() : bool
    {
        return $this->mode->is(DomainObjectRelationMode::TO_ONE);
    }

    /**
     * @return bool
     */
    public function isToMany() : bool
    {
        return $this->mode->is(DomainObjectRelationMode::TO_MANY);
    }

    /**
     * @return FinalizedPropertyDefinition
     */
    public function getDefinition() : FinalizedPropertyDefinition
    {
        return $this->definition;
    }

    /**
     * @return DomainObjectStructure
     */
    public function getRelatedObject() : DomainObjectStructure
    {
        return $this->relatedObject;
    }

    /**
     * @return bool
     */
    public function hasInverseRelation() : bool
    {
        return $this->inverseRelation !== null;
    }

    /**
     * @return DomainObjectRelation|null
     */
    public function getInverseRelation()
    {
        return $this->inverseRelation;
    }

    /**
     * @param DomainObjectRelation|null $inverseRelation
     */
    public function setInverseRelation(DomainObjectRelation $inverseRelation = null)
    {
        $this->inverseRelation = $inverseRelation;

        if ($this->inverseRelation) {
            $this->inverseRelation->inverseRelation = $this;
        }
    }
}
