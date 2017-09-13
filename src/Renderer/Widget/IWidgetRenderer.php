<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Widget;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Widget\IWidget;
use Dms\Web\Expressive\Http\ModuleContext;

/**
 * The widget renderer interface
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IWidgetRenderer
{
    /**
     * Returns whether this renderer can render the supplied widget.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return bool
     */
    public function accepts(ModuleContext $moduleContext, IWidget $widget) : bool;

    /**
     * Gets an array of links for the supplied widget.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getLinks(ModuleContext $moduleContext, IWidget $widget) : array;

    /**
     * Renders the supplied widget input as a html string.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(ModuleContext $moduleContext, IWidget $widget) : string;
}
