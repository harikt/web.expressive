<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Renderer\Package;

use Dms\Core\Exception\InvalidArgumentException;
use Dms\Core\Package\IPackage;

/**
 * The package dashboard renderer interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IPackageRenderer
{
    /**
     * Returns whether this renderer can render the supplied package.
     *
     * @param IPackage $package
     *
     * @return bool
     */
    public function accepts(IPackage $package) : bool;

    /**
     * Renders the supplied package dashboard as a html string.
     *
     * @param IPackage $package
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function render(IPackage $package) : string;
}
