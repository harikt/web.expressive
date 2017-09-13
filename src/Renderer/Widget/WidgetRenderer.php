<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Widget;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Widget\IWidget;
use Dms\Web\Expressive\Http\ModuleContext;

/**
 * The widget renderer base class.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class WidgetRenderer implements IWidgetRenderer
{
    /**
     * Gets an array of links for the supplied widget.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getLinks(ModuleContext $moduleContext, IWidget $widget) : array
    {
        if (!$this->accepts($moduleContext, $widget)) {
            throw InvalidArgumentException::format(
                'Invalid widget supplied to %s',
                get_class($this) . '::' . __FUNCTION__
            );
        }

        return $this->getWidgetLinks($moduleContext, $widget);
    }

    /**
     * Gets an array of links for the supplied widget.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return array
     */
    abstract protected function getWidgetLinks(ModuleContext $moduleContext, IWidget $widget) : array;

    /**
     * Renders the supplied widget input as a html string.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function render(ModuleContext $moduleContext, IWidget $widget) : string
    {
        if (!$this->accepts($moduleContext, $widget)) {
            throw InvalidArgumentException::format(
                'Invalid widget supplied to %s',
                get_class($this) . '::' . __FUNCTION__
            );
        }

        return $this->renderWidget($moduleContext, $widget);
    }

    /**
     * Renders the supplied widget input as a html string.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return string
     */
    abstract protected function renderWidget(ModuleContext $moduleContext, IWidget $widget) : string;
}
