<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Scaffold\CodeGeneration\Convention;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DefaultCodeConvention extends CodeConvention
{
    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function getPersistenceColumnName(string $propertyName) : string
    {
        return snake_case($propertyName);
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function getCmsFieldName(string $propertyName) : string
    {
        return snake_case($propertyName);
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function getCmsFieldLabel(string $propertyName) : string
    {
        return ucwords(str_replace('_', ' ', snake_case($propertyName)));
    }
}
