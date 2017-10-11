<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Package;

use Dms\Core\Package\IPackage;

/**
 * The default package renderer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DefaultPackageRenderer extends PackageRenderer
{
    /**
     * Returns whether this renderer can render the supplied package.
     *
     * @param IPackage $package
     *
     * @return bool
     */
    public function accepts(IPackage $package) : bool
    {
        return $package->hasDashboard();
    }

    /**
     * Renders the supplied package dashboard as a html string.
     *
     * @param IPackage $package
     *
     * @return string
     */
    protected function renderDashboard(IPackage $package) : string
    {
        $authorizedWidgets = [];

        foreach ($package->loadDashboard()->getWidgets() as $widget) {
            if ($widget->getWidget()->isAuthorized() && $widget->getModule()->isAuthorized()) {
                $authorizedWidgets[] = $widget;
            }
        }

        return $this->template->render(
            'dms::package.dashboard.default',
            [
                'widgets'         => $authorizedWidgets,
                'widgetRenderers' => $this->widgetRenderers,
            ]
        );
    }
}
