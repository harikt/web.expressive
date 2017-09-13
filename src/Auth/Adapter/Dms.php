<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Adapter;

use Dms\Core\Auth\IAdminRepository;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\Password\IPasswordHasherFactory;
use Aura\Auth\Adapter\AbstractAdapter;

class Dms extends AbstractAdapter
{
    /**
     * @var IAdminRepository
     */
    protected $userRepository;

    /**
     * @var IPasswordHasherFactory
     */
    protected $passwordHasherFactory;

    /**
     * @param IAdminRepository       $userRepository
     * @param IPasswordHasherFactory $passwordHasherFactory
     */
    public function __construct(
        IAdminRepository $userRepository,
        IPasswordHasherFactory $passwordHasherFactory
    ) {
        $this->userRepository        = $userRepository;
        $this->passwordHasherFactory = $passwordHasherFactory;
    }

    public function login(array $input)
    {
        $this->checkInput($input);

        $users = $this->userRepository->matching(
            $this->userRepository->criteria()
                ->where(Admin::USERNAME, '=', $input['username'])
        );

        if (count($users) !== 1) {
            return new Result(Result::FAILURE_IDENTITY_AMBIGUOUS, $this->username);
        }

        $user           = $users[0];
        $passwordHasher = $this->passwordHasherFactory->buildFor($user->getPassword());

        if ($passwordHasher->verify($password, $user->getPassword())) {
            return new Result(Result::SUCCESS, $user);
        }

        // if ($user->isBanned()) {
        //     throw AdminBannedException::defaultMessage($user);
        // }

        return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->username);
    }

    /**
     *
     * Handle logout logic against the storage backend.
     *
     * @param Auth $auth The authentication obbject to be logged out.
     *
     * @param string $status The new authentication status after logout.
     *
     * @return null
     *
     * @see Status
     *
     */
    public function logout(Auth $auth, $status = Status::ANON)
    {
        // do nothing
    }

    /**
     *
     * Handle a resumed session against the storage backend.
     *
     * @param Auth $auth The authentication object to be resumed.
     *
     * @return null
     *
     */
    public function resume(Auth $auth)
    {
        // do nothing
    }
}
