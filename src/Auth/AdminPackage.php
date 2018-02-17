<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth;

use Dms\Core\Package\Definition\PackageDefinition;
use Dms\Core\Package\Package;
use Dms\Web\Expressive\Auth\Module\AdminCurrentAccountModule;
use Dms\Web\Expressive\Auth\Module\AdminUserModule;
use Dms\Web\Expressive\Auth\Module\RoleModule;

/**
 * The auth package.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AdminPackage extends Package
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
        $package->name('admin');

        $package->metadata(
            [
            'icon' => 'rocket'
            ]
        );

        $package->dashboard()
            ->widgets(
                [
                'users.summary-table',
                'roles.summary-table',
                ]
            );

        $package->modules(
            [
            'account' => AdminCurrentAccountModule::class,
            'users'   => AdminUserModule::class,
            'roles'   => RoleModule::class,
            ]
        );
    }
}
