<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Module;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Web\Expressive\Http\ModuleContext;

/**
 * The module renderer collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ModuleRendererCollection
{
    /**
     * @var IModuleRenderer[]
     */
    protected $moduleRenderers;

    /**
     * ModuleRendererCollection constructor.
     *
     * @param IModuleRenderer[] $moduleRenderers
     */
    public function __construct(array $moduleRenderers)
    {
        InvalidArgumentException::verifyAllInstanceOf(
            __METHOD__,
            'moduleRenderers',
            $moduleRenderers,
            IModuleRenderer::class
        );

        $this->moduleRenderers = $moduleRenderers;
    }

    /**
     * @param ModuleContext $moduleContext
     *
     * @return IModuleRenderer
     * @throws UnrenderableModuleException
     */
    public function findRendererFor(ModuleContext $moduleContext) : IModuleRenderer
    {
        foreach ($this->moduleRenderers as $renderer) {
            if ($renderer->accepts($moduleContext)) {
                return $renderer;
            }
        }

        throw UnrenderableModuleException::format(
            'Could not render module of type %s with name \'%s\': no matching renderer could be found',
            get_class($moduleContext),
            $module->getName()
        );
    }
}
