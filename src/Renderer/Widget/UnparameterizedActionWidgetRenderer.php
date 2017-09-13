<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Widget;

use Dms\Core\Module\IUnparameterizedAction;
use Dms\Core\Widget\ActionWidget;
use Dms\Core\Widget\IWidget;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Util\KeywordTypeIdentifier;

/**
 * The widget renderer for unparameterized actions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnparameterizedActionWidgetRenderer extends WidgetRenderer
{
    /**
     * @var KeywordTypeIdentifier
     */
    protected $keywordTypeIdentifier;

    /**
     * UnparameterizedActionWidgetRenderer constructor.
     *
     * @param KeywordTypeIdentifier $keywordTypeIdentifier
     */
    public function __construct(KeywordTypeIdentifier $keywordTypeIdentifier)
    {
        $this->keywordTypeIdentifier = $keywordTypeIdentifier;
    }

    /**
     * Returns whether this renderer can render the supplied widget.
     *
     * @param IWidget $widget
     *
     * @return bool
     */
    public function accepts(ModuleContext $moduleContext, IWidget $widget) : bool
    {
        return $widget instanceof ActionWidget
        && $widget->getAction() instanceof IUnparameterizedAction;
    }

    /**
     * Gets an array of links for the supplied widget.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return array
     */
    protected function getWidgetLinks(ModuleContext $moduleContext, IWidget $widget) : array
    {
        return [];
    }

    /**
     * Renders the supplied widget input as a html string.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return string
     */
    protected function renderWidget(ModuleContext $moduleContext, IWidget $widget) : string
    {
        /** @var ActionWidget $widget */
        $action = $widget->getAction();

        return view('dms::components.widget.unparameterized-action')
            ->with([
                'action'      => $action,
                'actionUrl'   => $moduleContext->getUrl('action.run', [$action->getName()]),
                'buttonClass' => $this->keywordTypeIdentifier->getTypeFromName($action->getName()),
            ])
            ->render();
    }
}
