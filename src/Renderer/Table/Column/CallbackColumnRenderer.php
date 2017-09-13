<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Table\Column;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Web\Expressive\Renderer\Table\IColumnRenderer;

/**
 * The callback column renderer class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class CallbackColumnRenderer implements IColumnRenderer
{
    /**
     * @var callable
     */
    protected $renderHeaderCallback;

    /**
     * @var callable
     */
    protected $renderValueCallback;

    /**
     * CallbackColumnRenderer constructor.
     *
     * @param callable $renderHeaderCallback
     * @param callable $renderValueCallback
     */
    public function __construct(callable $renderHeaderCallback, callable $renderValueCallback)
    {
        $this->renderHeaderCallback = $renderHeaderCallback;
        $this->renderValueCallback = $renderValueCallback;
    }


    /**
     * Renders the header for the column as a html string
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function renderHeader() : string
    {
        return call_user_func($this->renderHeaderCallback);
    }

    /**
     * Renders the supplied column value as a html string.
     *
     * @param array $value
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(array $value) : string
    {
        return call_user_func($this->renderValueCallback, $value);
    }
}
