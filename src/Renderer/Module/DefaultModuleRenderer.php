<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Module;

use Dms\Web\Expressive\Http\ModuleContext;

/**
 * The default module renderer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DefaultModuleRenderer extends ModuleRenderer
{
    /**
     * Returns whether this renderer can render the supplied module.
     *
     * @param ModuleContext $moduleContext
     *
     * @return bool
     */
    public function accepts(ModuleContext $moduleContext) : bool
    {
        return true;
    }

    /**
     * Renders the supplied module dashboard as a html string.
     *
     * @param ModuleContext $moduleContext
     *
     * @return string
     */
    protected function renderDashboard(ModuleContext $moduleContext) : string
    {
        $authorizedWidgets = [];

        foreach ($moduleContext->getModule()->getWidgets() as $widget) {
            if ($widget->isAuthorized()) {
                $authorizedWidgets[] = $widget;
            }
        }

        return view('dms::package.module.dashboard.default')
            ->with([
                'moduleContext'   => $moduleContext,
                'widgets'         => $authorizedWidgets,
                'widgetRenderers' => $this->widgetRenderers,
            ])
            ->render();
    }
}
