<?php

namespace Dms\Web\Expressive\Renderer\Module;

use Dms\Common\Structure\FileSystem\PathHelper;
use Dms\Web\Expressive\Document\PublicFileModule;
use Dms\Web\Expressive\Http\ModuleContext;
use Dms\Web\Expressive\Renderer\Table\TableRenderer;
use Dms\Web\Expressive\Renderer\Widget\WidgetRendererCollection;

/**
 * The file tree module renderer.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FileTreeModuleRenderer extends ModuleRenderer
{
    /**
     * @var TableRenderer
     */
    protected $tableRenderer;

    /**
     * ReadModuleRenderer constructor.
     *
     * @param TableRenderer            $tableRenderer
     * @param WidgetRendererCollection $widgetRenderers
     */
    public function __construct(TableRenderer $tableRenderer, WidgetRendererCollection $widgetRenderers)
    {
        parent::__construct($widgetRenderers);
        $this->tableRenderer = $tableRenderer;
    }

    /**
     * Returns whether this renderer can render the supplied module.
     *
     * @param ModuleContext $moduleContext
     *
     * @return bool
     */
    public function accepts(ModuleContext $moduleContext) : bool
    {
        return $moduleContext->getModule() instanceof PublicFileModule;
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
        /** @var PublicFileModule $module */
        $module        = $moduleContext->getModule();
        $rootDirectory = $module->getRootDirectory();

        return view('dms::package.module.dashboard.file-tree')
            ->with([
                'isPublic'           => starts_with($rootDirectory, [
                    rtrim(PathHelper::normalize(config('dms.storage.public-files.dir')), '/\\'),
                    rtrim(public_path(), '/\\'),
                ]),
                'moduleContext'      => $moduleContext,
                'directoryTree'      => $module->getDirectoryTree(),
                'trashDirectoryTree' => $module->getTrashDirectoryTree(),
                'trashDataSource'    => $module->getTrashDataSource(),
                'rootDirectory'      => $rootDirectory,
            ])
            ->render();
    }
}
