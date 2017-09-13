<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table;

use Dms\Core\Exception\InvalidArgumentException;

/**
 * The column renderer interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IColumnRenderer
{
    /**
     * Renders the header for the column as a html string
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function renderHeader() : string;

    /**
     * Renders the supplied column value as a html string.
     *
     * @param array $value
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(array $value) : string;
}
