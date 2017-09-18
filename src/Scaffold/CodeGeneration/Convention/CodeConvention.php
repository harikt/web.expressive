<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration\Convention;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class CodeConvention
{
    /**
     * @param string $propertyName
     *
     * @return string
     */
    abstract public function getPersistenceColumnName(string $propertyName) : string;

    /**
     * @param string $propertyName
     *
     * @return string
     */
    abstract public function getCmsFieldName(string $propertyName) : string;

    /**
     * @param string $propertyName
     *
     * @return string
     */
    abstract public function getCmsFieldLabel(string $propertyName) : string;
}
