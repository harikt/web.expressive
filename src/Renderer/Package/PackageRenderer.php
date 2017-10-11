<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Package;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Package\IPackage;
use Dms\Web\Expressive\Renderer\Widget\WidgetRendererCollection;
use Zend\Expressive\Template\TemplateRendererInterface;

/**
 * The package dashboard renderer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class PackageRenderer implements IPackageRenderer
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
     * PackageRenderer constructor.
     *
     * @param WidgetRendererCollection  $widgetRenderers
     * @param TemplateRendererInterface $template
     */
    public function __construct(WidgetRendererCollection $widgetRenderers, TemplateRendererInterface $template)
    {
        $this->widgetRenderers = $widgetRenderers;
        $this->template = $template;
    }

    /**
     * Renders the supplied package dashboard as a html string.
     *
     * @param IPackage $package
     *
     * @return string
     * @throws InvalidArgumentException
     */
    final public function render(IPackage $package) : string
    {
        if (!$this->accepts($package)) {
            throw InvalidArgumentException::format(
                'Invalid package \'%s\' supplied to %s',
                $package->getName(),
                get_class($this) . '::' . __FUNCTION__
            );
        }

        return $this->renderDashboard($package);
    }

    /**
     * Renders the supplied package dashboard as a html string.
     *
     * @param IPackage $package
     *
     * @return string
     */
    abstract protected function renderDashboard(IPackage $package) : string;
}
