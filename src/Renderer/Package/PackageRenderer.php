<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Package;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Package\IPackage;
use Dms\Web\Expressive\Renderer\Widget\WidgetRendererCollection;

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
     * PackageRenderer constructor.
     *
     * @param WidgetRendererCollection $widgetRenderers
     */
    public function __construct(WidgetRendererCollection $widgetRenderers)
    {
        $this->widgetRenderers = $widgetRenderers;
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
