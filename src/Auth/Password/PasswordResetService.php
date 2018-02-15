<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Password;

use Dms\Core\Auth\IAdmin;
use Dms\Core\Auth\IAdminRepository;
use Dms\Core\Exception\InvalidArgumentException;
use Dms\Web\Expressive\Auth\LocalAdmin;

/**
 * The password reset service
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class PasswordResetService implements IPasswordResetService
{
    /**
     * @var IAdminRepository
     */
    private $adminRepository;

    /**
     * @var IPasswordHasherFactory
     */
    protected $hasherFactory;

    /**
     * PasswordResetService constructor.
     *
     * @param IAdminRepository       $userRepository
     * @param IPasswordHasherFactory $hasherFactory
     */
    public function __construct(IAdminRepository $userRepository, IPasswordHasherFactory $hasherFactory)
    {
        $this->adminRepository = $userRepository;
        $this->hasherFactory   = $hasherFactory;
    }

    /**
     * Resets the user's password.
     *
     * @param IAdmin $admin
     * @param string $newPassword
     *
     * @return void
     */
    public function resetUserPassword(IAdmin $admin, string $newPassword)
    {
        InvalidArgumentException::verifyInstanceOf(__METHOD__, 'admin', $admin, LocalAdmin::class);

        $hashedPassword = $this->hasherFactory->buildDefault()->hash($newPassword);

        /**
 * @var LocalAdmin $admin
*/
        $admin->setPassword($hashedPassword);

        $this->adminRepository->save($admin);
    }
}
