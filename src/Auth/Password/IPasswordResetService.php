<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Auth\IAdmin;

/**
 * The password reset service interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IPasswordResetService
{
    /**
     * Resets the user's password.
     *
     * @param IAdmin  $user
     * @param string $newPassword
     *
     * @return void
     */
    public function resetUserPassword(IAdmin $user, string $newPassword);
}
