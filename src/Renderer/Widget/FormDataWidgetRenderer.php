<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Widget;

use Dms\Core\Widget\FormDataWidget;
use Dms\Core\Widget\IWidget;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Form\DefaultFormRenderer;
use Dms\Web\Expressive\Renderer\Form\FormRenderingContext;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The form data renderer for parameterized actions.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FormDataWidgetRenderer extends WidgetRenderer
{
    /**
     * @var DefaultFormRenderer
     */
    protected $formRenderer;

    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * FormDataWidgetRenderer constructor.
     *
     * @param DefaultFormRenderer       $formRenderer
     * @param TemplateRendererInterface $template
     */
    public function __construct(DefaultFormRenderer $formRenderer, TemplateRendererInterface $template)
    {
        $this->formRenderer = $formRenderer;
        $this->template = $template;
    }

    /**
     * Returns whether this renderer can render the supplied widget.
     *
     * @param ModuleContext $moduleContext
     * @param IWidget       $widget
     *
     * @return bool
     */
    public function accepts(ModuleContext $moduleContext, IWidget $widget) : bool
    {
        return $widget instanceof FormDataWidget;
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
 * @var FormDataWidget $widget
*/
        $form             = $widget->getForm();
        $renderingContext = new FormRenderingContext($moduleContext);

        return $this->template->render(
            'dms::components.widget.form-data',
            [
                'action'          => $form,
                'formDataContent' => $this->formRenderer->renderFieldsAsValues($renderingContext, $form),
            ]
        );
    }
}
