<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Module;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Widget\WidgetRendererCollection;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The module dashboard renderer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class ModuleRenderer implements IModuleRenderer
{
    /**
     * @var WidgetRendererCollection
     */
    protected $widgetRenderers;

    /**
     * @var TemplateRendererInterface
     */
    protected $template;

    /**
     * ModuleRenderer constructor.
     *
     * @param WidgetRendererCollection  $widgetRenderers
     * @param TemplateRendererInterface $template
     */
    public function __construct(
        WidgetRendererCollection $widgetRenderers,
        TemplateRendererInterface $template
    ) {
        $this->widgetRenderers = $widgetRenderers;
        $this->template = $template;
    }

    /**
     * Renders the supplied module dashboard as a html string.
     *
     * @param ModuleContext $moduleContext
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function render(ModuleContext $moduleContext) : string
    {
        if (!$this->accepts($moduleContext)) {
            throw InvalidArgumentException::format(
                'Invalid module \'%s\' supplied to %s',
                $moduleContext->getModule()->getName(),
                get_class($this) . '::' . __FUNCTION__
            );
        }

        return $this->renderDashboard($moduleContext);
    }

    /**
     * Renders the supplied module dashboard as a html string.
     *
     * @param ModuleContext $moduleContext
     *
     * @return string
     */
    abstract protected function renderDashboard(ModuleContext $moduleContext) : string;
}
