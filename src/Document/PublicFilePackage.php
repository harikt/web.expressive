<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Document;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;

/**
 * The public file package.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PublicFilePackage extends Package
{
    /**
     * Defines the structure of this cms package.
     *
     * @param PackageDefinition $package
     *
     * @return void
     */
    protected function define(PackageDefinition $package)
    {
        $package->name('documents');

        $package->modules([
            'files' => PublicFileModule::class,
        ]);
    }
}
