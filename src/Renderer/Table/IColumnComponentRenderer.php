<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table;

use Dms\Core\Table\IColumnComponent;

/**
 * The column component renderer interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IColumnComponentRenderer
{
    /**
     * @param IColumnComponent $component
     *
     * @return bool
     */
    public function accepts(IColumnComponent $component) : bool;

    /**
     * Renders the supplied column component value as a html string.
     *
     * @param IColumnComponent $component
     * @param mixed            $value
     *
     * @return string
     */
    public function render(IColumnComponent $component, $value) : string;
}
