<?php declare(strict_types=1);

namespace Dms\Web\Expressive\Auth\Adapter;

use Dms\Core\Auth\IAdminRepository;
use Dms\Web\Expressive\Auth\Admin;
use Dms\Web\Expressive\Auth\Password\IPasswordHasherFactory;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class Db implements AdapterInterface
{
    private $password;

    private $username;

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

    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     */
    public function authenticate()
    {
        $users = $this->userRepository->matching(
            $this->userRepository->criteria()
                ->where(Admin::USERNAME, '=', $this->username)
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
}
