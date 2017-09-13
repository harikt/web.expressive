<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Package;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Package\IPackage;

/**
 * The package renderer collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PackageRendererCollection
{
    /**
     * @var IPackageRenderer[]
     */
    protected $packageRenderers;

    /**
     * PackageRendererCollection constructor.
     *
     * @param IPackageRenderer[] $packageRenderers
     */
    public function __construct(array $packageRenderers)
    {
        InvalidArgumentException::verifyAllInstanceOf(
            __METHOD__,
            'packageRenderers',
            $packageRenderers,
            IPackageRenderer::class
        );

        $this->packageRenderers = $packageRenderers;
    }

    /**
     * @param IPackage $package
     *
     * @return IPackageRenderer
     * @throws UnrenderablePackageException
     */
    public function findRendererFor(IPackage $package) : IPackageRenderer
    {
        foreach ($this->packageRenderers as $renderer) {
            if ($renderer->accepts($package)) {
                return $renderer;
            }
        }

        throw UnrenderablePackageException::format(
            'Could not render package of type %s with name \'%s\': no matching renderer could be found',
            get_class($package),
            $package->getName()
        );
    }
}
