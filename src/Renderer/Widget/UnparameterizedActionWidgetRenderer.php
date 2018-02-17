<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Widget;

use Dms\Core\Module\IUnparameterizedAction;
use Dms\Core\Widget\ActionWidget;
use Dms\Core\Widget\IWidget;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Util\KeywordTypeIdentifier;
use Zend\Expressive\Template\TemplateRendererInterface;

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
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * UnparameterizedActionWidgetRenderer constructor.
     *
     * @param KeywordTypeIdentifier     $keywordTypeIdentifier
     * @param TemplateRendererInterface $template
     */
    public function __construct(KeywordTypeIdentifier $keywordTypeIdentifier, TemplateRendererInterface $template)
    {
        $this->keywordTypeIdentifier = $keywordTypeIdentifier;
        $this->template = $template;
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
        /**
         * @var ActionWidget $widget
         */
        $action = $widget->getAction();

        return $this->template->render(
            'dms::components.widget.unparameterized-action',
            [
                'action'      => $action,
                'actionUrl'   => $moduleContext->getUrl('action.run', [$action->getName()]),
                'buttonClass' => $this->keywordTypeIdentifier->getTypeFromName($action->getName()),
            ]
        );
    }
}
