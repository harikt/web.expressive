<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table\Column\Component;

use Dms\Common\Structure\Type\Form\DomainSpecificStringType;
use Dms\Common\Structure\Type\StringValueObject;
use Dms\Common\Structure\Web\EmailAddress;
use Dms\Common\Structure\Web\Html;
use Dms\Common\Structure\Web\Url;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Form\Field\Type\BoolType;
use Dms\Core\Form\Field\Type\FloatType;
use Dms\Core\Form\Field\Type\IntType;
use Dms\Core\Form\Field\Type\StringType;
use Dms\Core\Model\Criteria\Condition\ConditionOperator;
use Dms\Core\Model\Type\Builder\Type;
use Dms\Core\Table\IColumnComponent;
use Dms\Core\Util\Debug;
use Dms\Web\Expressive\Renderer\Table\IColumnComponentRenderer;

/**
 * The scalar value component renderer.
 *
 * This is designed to be as quick as possible for rendering scalar
 * values, so blade views are not loaded as this could potentially
 * be called 1000's of times to load a large table.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OptimizedScalarValueComponentRenderer implements IColumnComponentRenderer
{
    /**
     * @param IColumnComponent $component
     *
     * @return bool
     */
    public function accepts(IColumnComponent $component) : bool
    {
        $fieldType     = $component->getType()->getOperator(ConditionOperator::EQUALS)->getField()->getType();

        return get_class($fieldType) === StringType::class
        || $fieldType instanceof DomainSpecificStringType
        || $fieldType instanceof IntType
        || $fieldType instanceof FloatType
        || $fieldType instanceof BoolType;
    }

    /**
     * Renders the supplied column component value as a html string.
     *
     * @param IColumnComponent $component
     * @param mixed            $value
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(IColumnComponent $component, $value) : string
    {
        $type = gettype($value);

        if ($type === 'string' || $type === 'integer' || $type === 'double') {
            return e($value);
        }

        if ($type === 'boolean') {
            return '<i class="fa fa-' . ($value ? 'check' : 'times') . '"></i>';
        }

        if ($type === 'NULL') {
            return '<i class="fa fa-times"></i>';
        }

        if ($value instanceof EmailAddress) {
            $url = e($value->asString());
            return '<a href="mailto:' . $url . '">' . $url . '</a>';
        }

        if ($value instanceof Url) {
            $url = e($value->asString());
            return '<a href="' . $url . '">' . $url . '</a>';
        }

        if ($value instanceof Html) {
            return $value->asString();
        }

        if ($value instanceof StringValueObject) {
            return e($value->asString());
        }

        throw InvalidArgumentException::format(
            'Cannot render value of type %s: type is not supported by %s',
            Debug::getType($value),
            __CLASS__
        );
    }
}
